<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'categoria',
        'descripcion',
    ];

    public const TIPOS = ['bool', 'string', 'int', 'json'];
    public const CACHE_KEY_ALL = 'settings.all';
    public const CACHE_TTL_SECONDS = 3600;

    /**
     * Obtiene un ajuste castedado según su tipo.
     * Cachea toda la tabla en memoria por 1h para evitar N+1.
     */
    public static function get(string $clave, mixed $default = null): mixed
    {
        $todos = Cache::remember(
            self::CACHE_KEY_ALL,
            self::CACHE_TTL_SECONDS,
            fn () => self::all()->keyBy('clave')->toArray()
        );

        if (! isset($todos[$clave])) {
            return $default;
        }

        return self::castearValor($todos[$clave]['valor'], $todos[$clave]['tipo']);
    }

    /**
     * Actualiza (o crea) un ajuste y limpia el cache.
     */
    public static function set(string $clave, mixed $valor, ?string $tipo = null): self
    {
        $tipoResuelto = $tipo ?? self::inferirTipo($valor);
        $valorSerializado = self::serializarValor($valor, $tipoResuelto);

        $setting = self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorSerializado,
                'tipo'  => $tipoResuelto,
            ],
        );

        self::forgetCache();

        return $setting;
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }

    /**
     * Getter del valor tipado en instancias.
     */
    public function getValorTipadoAttribute(): mixed
    {
        return self::castearValor($this->valor, $this->tipo);
    }

    // ── Helpers internos ──────────────────────────────────────────────

    private static function castearValor(?string $valor, string $tipo): mixed
    {
        return match ($tipo) {
            'bool'   => filter_var($valor, FILTER_VALIDATE_BOOLEAN),
            'int'    => (int) $valor,
            'json'   => json_decode($valor ?? 'null', true),
            default  => $valor,
        };
    }

    private static function serializarValor(mixed $valor, string $tipo): ?string
    {
        return match ($tipo) {
            'bool' => $valor ? 'true' : 'false',
            'int'  => (string) (int) $valor,
            'json' => json_encode($valor),
            default => is_string($valor) || is_null($valor) ? $valor : (string) $valor,
        };
    }

    private static function inferirTipo(mixed $valor): string
    {
        return match (true) {
            is_bool($valor)                    => 'bool',
            is_int($valor)                     => 'int',
            is_array($valor) || is_object($valor) => 'json',
            default                            => 'string',
        };
    }
}