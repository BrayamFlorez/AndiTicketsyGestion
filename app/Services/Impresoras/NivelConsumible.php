<?php

namespace App\Services\Impresoras;

use App\Models\ImpresoraConsumible;
use App\Models\ImpresoraConsumibleRecarga;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Nivel estimado de un consumible y recalibración por recarga parcial.
 *
 * El nivel baja en línea recta desde un punto de referencia:
 *   - Sin recargas parciales: 100 % en la fecha de instalación (como siempre).
 *   - Tras una recarga parcial: el % que quedó, en la fecha de la recarga.
 *
 *   nivel(hoy) = nivel_ref − (días desde la referencia ÷ días estimados) × 100
 *
 * Al recargar, lo que el técnico mide "antes" dice cuánto se gastó de verdad desde la
 * referencia; con eso se recalcula cuántos días dura el 100 % del consumible.
 */
class NivelConsumible
{
    /** Por debajo de esto el uso medido es muy poco para recalibrar con confianza */
    public const MIN_DIAS_PARA_RECALIBRAR = 2;
    public const MIN_CONSUMO_PARA_RECALIBRAR = 5; // puntos porcentuales

    // ─────────────────────────────────────────────────────────────────────
    //  Nivel estimado (para el accessor del modelo)
    // ─────────────────────────────────────────────────────────────────────

    /** Nivel estimado hoy, de 0 a 100, sin redondear. Null si falta información. */
    public function nivelEstimado(ImpresoraConsumible $consumible, ?CarbonInterface $ahora = null): ?float
    {
        [$nivelRef, $fechaRef] = $this->referencia($consumible);

        if ($fechaRef === null || ! $consumible->dias_estimados) {
            return null;
        }

        return self::nivelEn($nivelRef, self::diasEntre($fechaRef, $ahora ?? now()), (int) $consumible->dias_estimados);
    }

    /** Días que le quedan al consumible según el nivel estimado. */
    public function diasRestantes(ImpresoraConsumible $consumible, ?CarbonInterface $ahora = null): ?int
    {
        $nivel = $this->nivelEstimado($consumible, $ahora);

        return $nivel === null ? null : (int) round($nivel / 100 * $consumible->dias_estimados);
    }

    /** Días transcurridos desde el punto de referencia (con decimales). */
    public function diasDesdeReferencia(ImpresoraConsumible $consumible, ?CarbonInterface $ahora = null): ?float
    {
        [, $fechaRef] = $this->referencia($consumible);

        return $fechaRef ? self::diasEntre($fechaRef, $ahora ?? now()) : null;
    }

    /** [nivel de referencia, fecha de referencia] */
    public function referencia(ImpresoraConsumible $consumible): array
    {
        if ($consumible->fecha_referencia_nivel !== null && $consumible->nivel_referencia !== null) {
            return [(float) $consumible->nivel_referencia, $consumible->fecha_referencia_nivel];
        }

        return [100.0, $consumible->fecha_instalacion];
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Recarga parcial
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Calcula la recarga sin guardar nada.
     *
     * @return array{
     *   nivel_estimado_sistema: float, nivel_antes: float, nivel_despues: float, porcion_recargada: float,
     *   porcion_dias: int, desviacion_porcentaje: float, desviacion_dias: int, dias_desde_referencia: float,
     *   dias_estimados_anteriores: int, dias_estimados_nuevos: int, recalibrado: bool,
     *   motivo_sin_recalibrar: ?string, dias_restantes_estimados: int
     * }
     */
    public function calcularRecarga(ImpresoraConsumible $consumible, float $antes, float $despues, ?CarbonInterface $ahora = null): array
    {
        [$nivelRef] = $this->referencia($consumible);

        return self::calcular(
            nivelReferencia: $nivelRef,
            diasDesdeReferencia: $this->diasDesdeReferencia($consumible, $ahora) ?? 0.0,
            diasEstimados: (int) $consumible->dias_estimados,
            antes: $antes,
            despues: $despues,
        );
    }

    /** Registra la recarga, recalibra el consumible y mueve su punto de referencia a ahora. */
    public function aplicarRecarga(ImpresoraConsumible $consumible, float $antes, float $despues, ?int $usuarioId, ?string $observaciones = null): ImpresoraConsumibleRecarga
    {
        return DB::transaction(function () use ($consumible, $antes, $despues, $usuarioId, $observaciones) {
            $ahora = now();
            $calculo = $this->calcularRecarga($consumible, $antes, $despues, $ahora);

            $recarga = ImpresoraConsumibleRecarga::create([
                'impresora_id'              => $consumible->impresora_id,
                'consumible_id'             => $consumible->id,
                'nivel_estimado_sistema'    => $calculo['nivel_estimado_sistema'],
                'nivel_antes'               => $calculo['nivel_antes'],
                'nivel_despues'             => $calculo['nivel_despues'],
                'porcion_recargada'         => $calculo['porcion_recargada'],
                'desviacion_porcentaje'     => $calculo['desviacion_porcentaje'],
                'desviacion_dias'           => $calculo['desviacion_dias'],
                'dias_desde_referencia'     => $calculo['dias_desde_referencia'],
                'dias_estimados_anteriores' => $calculo['dias_estimados_anteriores'],
                'dias_estimados_nuevos'     => $calculo['dias_estimados_nuevos'],
                'recalibrado'               => $calculo['recalibrado'],
                'dias_restantes_estimados'  => $calculo['dias_restantes_estimados'],
                'observaciones'             => $observaciones,
                'registrado_por'            => $usuarioId,
            ]);

            // fecha_instalacion no se toca: sigue sirviendo para la duración real al reemplazar
            $consumible->forceFill([
                'dias_estimados'         => $calculo['dias_estimados_nuevos'],
                'nivel_referencia'       => $calculo['nivel_despues'],
                'fecha_referencia_nivel' => $ahora,
            ])->save();

            return $recarga;
        });
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Fórmulas (puras: la vista previa del modal usa exactamente las mismas)
    // ─────────────────────────────────────────────────────────────────────

    public static function nivelEn(float $nivelReferencia, float $dias, int $diasEstimados): float
    {
        if ($diasEstimados <= 0) {
            return 0.0;
        }

        return max(0.0, min(100.0, $nivelReferencia - $dias / $diasEstimados * 100));
    }

    public static function calcular(float $nivelReferencia, float $diasDesdeReferencia, int $diasEstimados, float $antes, float $despues): array
    {
        $estimado = self::nivelEn($nivelReferencia, $diasDesdeReferencia, $diasEstimados);

        // Lo que se gastó de verdad desde la referencia
        $consumoReal = $nivelReferencia - $antes;

        $recalibrado = false;
        $motivo = null;
        $diasNuevos = $diasEstimados;

        if ($diasDesdeReferencia < self::MIN_DIAS_PARA_RECALIBRAR) {
            $motivo = 'Pasó muy poco tiempo desde la instalación o la última recarga para recalibrar.';
        } elseif ($consumoReal < self::MIN_CONSUMO_PARA_RECALIBRAR) {
            $motivo = 'El consumo medido es muy bajo para recalibrar con confianza.';
        } else {
            // Si en D días se gastó C %, el 100 % dura D × 100 ÷ C días
            $diasNuevos = (int) max(1, min(3650, round($diasDesdeReferencia * 100 / $consumoReal)));
            $recalibrado = true;
        }

        $desviacion = $estimado - $antes;

        return [
            'nivel_estimado_sistema'    => round($estimado, 2),
            'nivel_antes'               => round($antes, 2),
            'nivel_despues'             => round($despues, 2),
            'porcion_recargada'         => round($despues - $antes, 2),
            'porcion_dias'              => (int) round(($despues - $antes) / 100 * $diasNuevos),
            // Positiva: se gastó más rápido de lo estimado; negativa: más lento
            'desviacion_porcentaje'     => round($desviacion, 2),
            'desviacion_dias'           => (int) round($desviacion / 100 * $diasEstimados),
            'dias_desde_referencia'     => round($diasDesdeReferencia, 2),
            'dias_estimados_anteriores' => $diasEstimados,
            'dias_estimados_nuevos'     => $diasNuevos,
            'recalibrado'               => $recalibrado,
            'motivo_sin_recalibrar'     => $motivo,
            'dias_restantes_estimados'  => (int) round($despues / 100 * $diasNuevos),
        ];
    }

    /** Días calendario completos: el mismo criterio con el que el show muestra el nivel. */
    private static function diasEntre(CarbonInterface $desde, CarbonInterface $hasta): float
    {
        return (float) max(0, (int) round($desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay(), false)));
    }
}
