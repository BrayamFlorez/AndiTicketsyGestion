<?php

namespace App\Http\Requests\Agente;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base de las peticiones del agente: todas traen el inventario del PC.
 * El agente siempre envía "Accept: application/json", así que los errores vuelven como 422 JSON.
 */
abstract class AgenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización la hacen los middlewares: llave de la empresa (instalación) o token del PC (reporte)
        return true;
    }

    protected function reglasInventario(): array
    {
        return [
            'inventario'                => ['required', 'array'],
            'inventario.hostname'       => ['required', 'string', 'max:100'],
            'inventario.marca'          => ['nullable', 'string', 'max:100'],
            'inventario.modelo'         => ['nullable', 'string', 'max:100'],
            'inventario.numero_serie'   => ['nullable', 'string', 'max:100'],
            'inventario.procesador'     => ['nullable', 'string', 'max:200'],
            'inventario.sistema'        => ['nullable', 'string', 'max:200'],
            'inventario.ram'            => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'inventario.disco'          => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'inventario.tipo_disco'     => ['nullable', 'string', 'max:50'],
            'inventario.bus_disco'      => ['nullable', 'string', 'max:50'],
            'inventario.usuario'        => ['nullable', 'string', 'max:150'],
            'inventario.dominio'        => ['nullable', 'string', 'max:150'],
            'inventario.ip'             => ['nullable', 'ip'],
            'inventario.mac'            => ['nullable', 'string', 'max:17'],
            'inventario.version_agente' => ['nullable', 'string', 'max:20'],
            // escritorio | portatil | todo_en_uno (para sugerir la categoría)
            'inventario.chasis'         => ['nullable', 'string', 'max:30'],
        ];
    }

    public function inventario(): array
    {
        return $this->validated('inventario');
    }

    public function attributes(): array
    {
        return [
            'inventario.hostname'     => 'nombre del equipo',
            'inventario.numero_serie' => 'número de serie',
        ];
    }
}
