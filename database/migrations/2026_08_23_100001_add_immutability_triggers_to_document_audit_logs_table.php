<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Defensa en profundidad: aunque toda la aplicación pase por
     * App\Models\DocumentAuditLog (que ya bloquea update()/delete()), esta
     * migración agrega triggers a nivel de motor de base de datos que
     * rechazan cualquier UPDATE o DELETE sobre document_audit_logs, incluso
     * si alguien se conecta directo con un cliente SQL o con credenciales
     * de administrador de base de datos.
     *
     * Soporta MySQL (producción, ver .env: DB_CONNECTION=mysql) y SQLite
     * (usado en tests, ver phpunit.xml: DB_CONNECTION=sqlite). En cualquier
     * otro motor, la migración no falla: simplemente no crea triggers y la
     * inmutabilidad queda a cargo únicamente de las capas de aplicación.
     *
     * Recomendación adicional de producción (no automatizable desde una
     * migración): el usuario de base de datos con el que corre la
     * aplicación debería tener permisos de solo INSERT y SELECT sobre esta
     * tabla, por ejemplo:
     *
     *   REVOKE UPDATE, DELETE ON tickets.document_audit_logs FROM 'app_user'@'%';
     *   GRANT INSERT, SELECT ON tickets.document_audit_logs TO 'app_user'@'%';
     */
    private array $triggers = [
        'trg_document_audit_logs_no_update',
        'trg_document_audit_logs_no_delete',
    ];

    public function up(): void
    {
        match (DB::connection()->getDriverName()) {
            'mysql'  => $this->crearTriggersMysql(),
            'sqlite' => $this->crearTriggersSqlite(),
            default  => null,
        };
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'sqlite'], true)) {
            return;
        }

        foreach ($this->triggers as $trigger) {
            DB::unprepared("DROP TRIGGER IF EXISTS {$trigger}");
        }
    }

    private function crearTriggersMysql(): void
    {
        DB::unprepared('
            CREATE TRIGGER trg_document_audit_logs_no_update
            BEFORE UPDATE ON document_audit_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE \'45000\'
                SET MESSAGE_TEXT = \'document_audit_logs es inmutable: UPDATE bloqueado a nivel de base de datos.\';
            END
        ');

        DB::unprepared('
            CREATE TRIGGER trg_document_audit_logs_no_delete
            BEFORE DELETE ON document_audit_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE \'45000\'
                SET MESSAGE_TEXT = \'document_audit_logs es inmutable: DELETE bloqueado a nivel de base de datos.\';
            END
        ');
    }

    private function crearTriggersSqlite(): void
    {
        DB::unprepared("
            CREATE TRIGGER trg_document_audit_logs_no_update
            BEFORE UPDATE ON document_audit_logs
            BEGIN
                SELECT RAISE(ABORT, 'document_audit_logs es inmutable: UPDATE bloqueado a nivel de base de datos.');
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_document_audit_logs_no_delete
            BEFORE DELETE ON document_audit_logs
            BEGIN
                SELECT RAISE(ABORT, 'document_audit_logs es inmutable: DELETE bloqueado a nivel de base de datos.');
            END;
        ");
    }
};
