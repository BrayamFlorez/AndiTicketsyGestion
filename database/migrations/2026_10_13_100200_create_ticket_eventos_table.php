<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Línea de tiempo del ticket: cada cambio de estado o de técnico queda registrado,
 * con quién lo hizo y un comentario opcional (motivo de reasignación, partes, etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('tipo', 30);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado_anterior', 30)->nullable();
            $table->string('estado_nuevo', 30)->nullable();
            $table->foreignId('tecnico_anterior_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tecnico_nuevo_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comentario')->nullable();
            $table->json('datos')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ticket_id', 'created_at']);
        });

        $this->reconstruirHistorialExistente();
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_eventos');
    }

    /** Los tickets que ya existen arrancan con la historia que se deduce de sus fechas. */
    private function reconstruirHistorialExistente(): void
    {
        DB::table('tickets')->orderBy('id')->chunkById(500, function ($tickets) {
            $filas = [];

            foreach ($tickets as $t) {
                $evento = fn (string $tipo, $fecha, ?int $usuario, ?string $estado, array $extra = []) => array_merge([
                    'ticket_id'           => $t->id,
                    'tipo'                => $tipo,
                    'usuario_id'          => $usuario,
                    'estado_anterior'     => null,
                    'estado_nuevo'        => $estado,
                    'tecnico_anterior_id' => null,
                    'tecnico_nuevo_id'    => null,
                    'comentario'          => null,
                    'datos'               => null,
                    'created_at'          => $fecha,
                ], $extra);

                $filas[] = $evento('creado', $t->created_at, $t->solicitante_id, 'abierto');

                if ($t->asignado_at && $t->asignado_a) {
                    $filas[] = $evento('asignado', $t->asignado_at, null, 'asignado', ['tecnico_nuevo_id' => $t->asignado_a]);
                }
                if ($t->iniciado_at) {
                    $filas[] = $evento('iniciado', $t->iniciado_at, $t->asignado_a, 'en_progreso');
                }
                if ($t->resuelto_at) {
                    $filas[] = $evento('resuelto', $t->resuelto_at, $t->asignado_a, 'resuelto');
                }
                if ($t->cerrado_at) {
                    $filas[] = $evento('cerrado', $t->cerrado_at, null, 'cerrado');
                }
                if ($t->estado === 'cancelado') {
                    $filas[] = $evento('cancelado', $t->updated_at, null, 'cancelado');
                }
            }

            DB::table('ticket_eventos')->insert($filas);
        });
    }
};
