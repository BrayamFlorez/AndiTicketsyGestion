<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImpresoraAlertaEnviada extends Model
{
    protected $table = 'impresora_alertas_enviadas';

    public $timestamps = false;

    protected $fillable = [
        'impresora_id', 'tipo', 'referencia_id', 'enviada_en', 'resuelta_en',
    ];

    protected $casts = [
        'enviada_en'  => 'datetime',
        'resuelta_en' => 'datetime',
    ];

    public const TIPO_CONSUMIBLE_BAJO       = 'consumible_bajo';
    public const TIPO_MANTENIMIENTO_PROXIMO = 'mantenimiento_proximo';

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    /**
     * ¿Ya se envió una alerta reciente (última hora, día, etc.) para esta combinación?
     */
    public static function yaEnviadaRecientemente(
        int $impresoraId,
        string $tipo,
        ?int $referenciaId,
        int $horasVentana = 72,
    ): bool {
        return self::where('impresora_id', $impresoraId)
            ->where('tipo', $tipo)
            ->where('referencia_id', $referenciaId)
            ->whereNull('resuelta_en')
            ->where('enviada_en', '>=', now()->subHours($horasVentana))
            ->exists();
    }

    /**
     * Marca como resueltas todas las alertas pendientes de un tipo específico.
     */
    public static function resolverAlertas(int $impresoraId, string $tipo, ?int $referenciaId = null): void
    {
        $query = self::where('impresora_id', $impresoraId)
            ->where('tipo', $tipo)
            ->whereNull('resuelta_en');

        if ($referenciaId !== null) {
            $query->where('referencia_id', $referenciaId);
        }

        $query->update(['resuelta_en' => now()]);
    }
}