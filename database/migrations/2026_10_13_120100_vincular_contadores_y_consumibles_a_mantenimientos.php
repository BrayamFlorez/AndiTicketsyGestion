<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * - impresora_contadores.mantenimiento_id: de qué mantenimiento vino la lectura (null = registro manual).
 * - impresora_consumibles.mantenimiento_id: en qué mantenimiento se instaló el consumible (para el reporte).
 *
 * Además completa el histórico de contadores con las lecturas de los mantenimientos anteriores,
 * con la fecha en que se hizo el mantenimiento, y recalcula las páginas entre lecturas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impresora_contadores', function (Blueprint $table) {
            $table->foreignId('mantenimiento_id')->nullable()->after('impresora_id')
                ->constrained('impresora_mantenimientos')->nullOnDelete();
        });

        Schema::table('impresora_consumibles', function (Blueprint $table) {
            $table->foreignId('mantenimiento_id')->nullable()
                ->constrained('impresora_mantenimientos')->nullOnDelete();
        });

        $this->vincularConsumibles();
        $this->completarHistoricoContadores();
    }

    public function down(): void
    {
        Schema::table('impresora_consumibles', fn (Blueprint $t) => $t->dropConstrainedForeignId('mantenimiento_id'));
        Schema::table('impresora_contadores', fn (Blueprint $t) => $t->dropConstrainedForeignId('mantenimiento_id'));
    }

    /** Hasta ahora el vínculo solo estaba en el texto "Reemplazado en mantenimiento #ID" */
    private function vincularConsumibles(): void
    {
        DB::table('impresora_consumibles')
            ->where('observaciones', 'like', 'Reemplazado en mantenimiento #%')
            ->orderBy('id')
            ->get(['id', 'observaciones'])
            ->each(function ($c) {
                if (preg_match('/mantenimiento #(\d+)/', $c->observaciones, $m)
                    && DB::table('impresora_mantenimientos')->where('id', $m[1])->exists()) {
                    DB::table('impresora_consumibles')->where('id', $c->id)->update(['mantenimiento_id' => (int) $m[1]]);
                }
            });
    }

    private function completarHistoricoContadores(): void
    {
        $mantenimientos = DB::table('impresora_mantenimientos')
            ->whereNotNull('contador_paginas')
            ->orderBy('fecha_realizado')
            ->get(['id', 'impresora_id', 'contador_paginas', 'fecha_realizado', 'registrado_por', 'created_at']);

        foreach ($mantenimientos as $m) {
            // Si la lectura ya está (registrada desde el mantenimiento), solo se vincula
            $existente = DB::table('impresora_contadores')
                ->where('impresora_id', $m->impresora_id)
                ->where('contador', $m->contador_paginas)
                ->whereNull('mantenimiento_id')
                ->first();

            if ($existente) {
                DB::table('impresora_contadores')->where('id', $existente->id)->update(['mantenimiento_id' => $m->id]);
                continue;
            }

            $fecha = Carbon::parse($m->fecha_realizado)->setTimeFrom(Carbon::parse($m->created_at ?? now()));

            DB::table('impresora_contadores')->insert([
                'impresora_id'     => $m->impresora_id,
                'mantenimiento_id' => $m->id,
                'contador'         => $m->contador_paginas,
                'reinicio'         => false,
                'registrado_por'   => $m->registrado_por,
                'created_at'       => $fecha,
                'updated_at'       => $fecha,
            ]);
        }

        // Páginas entre lecturas, en orden cronológico por impresora
        DB::table('impresora_contadores')->distinct()->pluck('impresora_id')->each(function ($impresoraId) {
            $anterior = null;

            DB::table('impresora_contadores')->where('impresora_id', $impresoraId)
                ->orderBy('created_at')->orderBy('id')
                ->get(['id', 'contador', 'reinicio'])
                ->each(function ($lectura) use (&$anterior) {
                    $paginas = $anterior !== null && ! $lectura->reinicio && $lectura->contador >= $anterior
                        ? $lectura->contador - $anterior
                        : null;

                    DB::table('impresora_contadores')->where('id', $lectura->id)->update([
                        'paginas_desde_anterior' => $paginas,
                        // Un contador menor sin marcar reinicio solo puede ser un reinicio
                        'reinicio' => $lectura->reinicio || ($anterior !== null && $lectura->contador < $anterior),
                    ]);

                    $anterior = $lectura->contador;
                });
        });
    }
};
