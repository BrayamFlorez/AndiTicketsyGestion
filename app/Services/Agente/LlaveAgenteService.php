<?php

namespace App\Services\Agente;

use App\Models\Empresa;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Llave del agente por empresa: "agk_" + 48 caracteres aleatorios (~285 bits).
 *  - Para buscar la empresa se usa el hash sha256 (nunca se compara en claro).
 *  - Se guarda además cifrada con APP_KEY para que el administrador pueda volver a verla y copiarla.
 */
class LlaveAgenteService
{
    private const PREFIJO = 'agk_';

    /** Genera una llave nueva. La anterior deja de servir para instalar (los PCs ya instalados siguen). */
    public function generar(Empresa $empresa): string
    {
        $llave = self::PREFIJO . Str::random(48);

        $empresa->forceFill([
            'agente_llave_hash'        => $this->hash($llave),
            'agente_llave_cifrada'     => Crypt::encryptString($llave),
            'agente_llave_generada_at' => now(),
        ])->save();

        return $llave;
    }

    public function revocar(Empresa $empresa): void
    {
        $empresa->forceFill([
            'agente_llave_hash'        => null,
            'agente_llave_cifrada'     => null,
            'agente_llave_generada_at' => null,
        ])->save();
    }

    /** La llave en claro, para mostrarla al administrador. Null si no tiene. */
    public function revelar(Empresa $empresa): ?string
    {
        return $empresa->agente_llave_cifrada ? Crypt::decryptString($empresa->agente_llave_cifrada) : null;
    }

    public function empresaPorLlave(?string $llave): ?Empresa
    {
        if (! $llave || ! str_starts_with($llave, self::PREFIJO)) {
            return null;
        }

        return Empresa::query()
            ->where('agente_llave_hash', $this->hash($llave))
            ->where('activo', true)
            ->first();
    }

    private function hash(string $llave): string
    {
        return hash('sha256', $llave);
    }
}
