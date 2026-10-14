<?php

namespace App\Console\Commands;

use App\Models\Impresora;
use App\Models\ImpresoraAlertaEnviada;
use App\Models\User;
use App\Notifications\Impresoras\AlertaImpresoraNotification;
use App\Support\Roles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EnviarAlertasImpresoras extends Command
{
    protected $signature = 'impresoras:alertas
                            {--dry-run : Muestra qué alertas se enviarían sin dispararlas}';

    protected $description = 'Envía alertas por consumibles bajos y mantenimientos próximos a técnicos con cobertura.';

    // Umbral de nivel bajo (%)
    private const UMBRAL_NIVEL      = 20;
    // Días de anticipación para mantenimiento
    private const DIAS_MANTTO_AVISO = 7;
    // Anti-spam: horas mínimas entre alertas del mismo tipo/referencia
    private const HORAS_VENTANA     = 72;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $enviadas = 0;

        $this->info('▶  Buscando impresoras con alertas pendientes...');

        // Cargar todas las impresoras activas con sus consumibles activos y empresa
        $impresoras = Impresora::where('estado', Impresora::ESTADO_ACTIVA)
            ->with(['consumiblesActivos', 'empresa:id,nombre'])
            ->get();

        foreach ($impresoras as $impresora) {
            $enviadas += $this->procesarConsumibles($impresora, $dryRun);
            $enviadas += $this->procesarMantenimiento($impresora, $dryRun);
        }

        $accion = $dryRun ? 'Se enviarían' : 'Se enviaron';
        $this->info("✔  {$accion} {$enviadas} notificaciones.");

        return self::SUCCESS;
    }

    private function procesarConsumibles(Impresora $impresora, bool $dryRun): int
    {
        $enviadas = 0;

        foreach ($impresora->consumiblesActivos as $consumible) {
            $nivel = $consumible->nivel_estimado;

            // Skip si no calcula (sin fecha_instalacion) o nivel está bien
            if ($nivel === null || $nivel > self::UMBRAL_NIVEL) {
                continue;
            }

            // Anti-spam
            if (ImpresoraAlertaEnviada::yaEnviadaRecientemente(
                $impresora->id,
                ImpresoraAlertaEnviada::TIPO_CONSUMIBLE_BAJO,
                $consumible->id,
                self::HORAS_VENTANA,
            )) {
                continue;
            }

            $tecnicos = $this->tecnicosConCobertura($impresora);

            if ($tecnicos->isEmpty()) {
                continue;
            }

            $detalle = [
                'consumible'   => $consumible->descripcion_corta,
                'nivel'        => $nivel,
                'dias_restantes' => $consumible->dias_restantes,
            ];

            $this->line(sprintf(
                '  [%s] Consumible "%s" al %d%% — %d técnicos%s',
                $impresora->codigo,
                $consumible->descripcion_corta,
                $nivel,
                $tecnicos->count(),
                $dryRun ? ' (dry-run)' : '',
            ));

            if (! $dryRun) {
                Notification::send(
                    $tecnicos,
                    new AlertaImpresoraNotification(
                        $impresora,
                        ImpresoraAlertaEnviada::TIPO_CONSUMIBLE_BAJO,
                        $detalle,
                    ),
                );

                ImpresoraAlertaEnviada::create([
                    'impresora_id'  => $impresora->id,
                    'tipo'          => ImpresoraAlertaEnviada::TIPO_CONSUMIBLE_BAJO,
                    'referencia_id' => $consumible->id,
                    'enviada_en'    => now(),
                ]);
            }

            $enviadas += $tecnicos->count();
        }

        return $enviadas;
    }

    private function procesarMantenimiento(Impresora $impresora, bool $dryRun): int
    {
        if (! $impresora->proximo_mantenimiento_sugerido) {
            return 0;
        }

        $diasHasta = now()->startOfDay()->diffInDays(
            $impresora->proximo_mantenimiento_sugerido->startOfDay(),
            false,
        );

        // Solo alertar si está dentro de la ventana o vencido
        if ($diasHasta > self::DIAS_MANTTO_AVISO) {
            return 0;
        }

        // Anti-spam
        if (ImpresoraAlertaEnviada::yaEnviadaRecientemente(
            $impresora->id,
            ImpresoraAlertaEnviada::TIPO_MANTENIMIENTO_PROXIMO,
            null,
            self::HORAS_VENTANA,
        )) {
            return 0;
        }

        $tecnicos = $this->tecnicosConCobertura($impresora);
        if ($tecnicos->isEmpty()) {
            return 0;
        }

        $detalle = [
            'fecha'      => $impresora->proximo_mantenimiento_sugerido->format('d/m/Y'),
            'dias_hasta' => $diasHasta,
            'vencido'    => $diasHasta < 0,
        ];

        $this->line(sprintf(
            '  [%s] Mantto sugerido %s — %d técnicos%s',
            $impresora->codigo,
            $impresora->proximo_mantenimiento_sugerido->format('d/m/Y'),
            $tecnicos->count(),
            $dryRun ? ' (dry-run)' : '',
        ));

        if (! $dryRun) {
            Notification::send(
                $tecnicos,
                new AlertaImpresoraNotification(
                    $impresora,
                    ImpresoraAlertaEnviada::TIPO_MANTENIMIENTO_PROXIMO,
                    $detalle,
                ),
            );

            ImpresoraAlertaEnviada::create([
                'impresora_id' => $impresora->id,
                'tipo'         => ImpresoraAlertaEnviada::TIPO_MANTENIMIENTO_PROXIMO,
                'enviada_en'   => now(),
            ]);
        }

        return $tecnicos->count();
    }

    /**
     * Técnicos con cobertura sobre la empresa de la impresora.
     */
    private function tecnicosConCobertura(Impresora $impresora)
    {
        return User::where('activo', true)
            ->role(Roles::CON_COBERTURA)
            ->whereExists(function ($sub) use ($impresora) {
                $sub->from('empresa_user')
                    ->whereColumn('empresa_user.user_id', 'users.id')
                    ->where('empresa_user.empresa_id', $impresora->empresa_id);
            })
            ->get();
    }
}