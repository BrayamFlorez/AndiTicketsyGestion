<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Error de negocio del agente. Laravel llama a render() y responde JSON con un
 * "codigo" estable que el agente interpreta (no depende del texto del mensaje).
 */
class AgenteException extends RuntimeException
{
    public function __construct(
        public readonly string $codigo,
        string $mensaje,
        public readonly int $status = 422,
    ) {
        parent::__construct($mensaje);
    }

    public static function llaveInvalida(): self
    {
        return new self('llave_invalida', 'El token de la empresa no es válido, fue regenerado o la empresa está inactiva.', 401);
    }

    public static function sinEquipoParaReemplazar(): self
    {
        return new self('sin_equipo_para_reemplazar',
            'No hay un equipo con el serial de este PC en la empresa. Regístrelo como nuevo.', 409);
    }

    public static function sedeInvalida(): self
    {
        return new self('sede_invalida', 'La sede elegida no pertenece a la empresa o está inactiva.', 422);
    }

    public static function noSePuedeCrear(string $motivo): self
    {
        return new self('no_se_puede_crear', $motivo, 422);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'codigo'  => $this->codigo,
            'mensaje' => $this->getMessage(),
        ], $this->status);
    }
}
