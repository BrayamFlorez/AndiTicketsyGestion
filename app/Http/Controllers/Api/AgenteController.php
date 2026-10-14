<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agente\RegistrarAgenteRequest;
use App\Http\Requests\Agente\ReporteAgenteRequest;
use App\Http\Requests\Agente\VerificarAgenteRequest;
use App\Models\Empresa;
use App\Models\EquipoAgente;
use App\Services\Agente\AgenteInventarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API que consume el agente de Windows.
 *
 *  Instalación (Bearer = llave de la empresa):
 *   POST /api/agente/v1/registro/verificar  → empresa y si el serial ya existe
 *   POST /api/agente/v1/registro            → crea o toma el equipo y devuelve el token del PC
 *
 *  Funcionamiento (Bearer = token del PC):
 *   POST /api/agente/v1/reporte             → actualiza el equipo
 *   GET  /api/agente/v1/estado              → equipo vinculado
 *   POST /api/agente/v1/desvincular         → al desinstalar: el token deja de servir
 */
class AgenteController extends Controller
{
    public function __construct(private readonly AgenteInventarioService $servicio) {}

    public function verificar(VerificarAgenteRequest $request): JsonResponse
    {
        return response()->json($this->servicio->verificar($this->empresa($request), $request->inventario()));
    }

    public function registrar(RegistrarAgenteRequest $request): JsonResponse
    {
        [$agente, $token] = $this->servicio->registrar(
            $this->empresa($request),
            $request->inventario(),
            $request->validated('accion'),
            $request->validated('sede_id') !== null ? (int) $request->validated('sede_id') : null,
            $request->validated('categoria'),
        );

        return response()->json([
            'token'             => $token,
            'equipo'            => $this->equipo($agente),
            'intervalo_minutos' => config('agente.intervalo_minutos'),
        ], 201);
    }

    public function reporte(ReporteAgenteRequest $request): JsonResponse
    {
        $cambios = $this->servicio->registrarReporte($this->agente($request), $request->inventario());

        return response()->json([
            'cambios'           => array_keys($cambios),
            'intervalo_minutos' => config('agente.intervalo_minutos'),
        ]);
    }

    public function estado(Request $request): JsonResponse
    {
        $agente = $this->agente($request);

        return response()->json([
            'equipo'            => $this->equipo($agente),
            'ultimo_reporte'    => $agente->ultimo_reporte_at?->toIso8601String(),
            'intervalo_minutos' => config('agente.intervalo_minutos'),
        ]);
    }

    public function desvincular(Request $request): JsonResponse
    {
        $this->servicio->desvincular($this->agente($request));

        return response()->json(['desvinculado' => true]);
    }

    private function empresa(Request $request): Empresa
    {
        return $request->attributes->get('empresa');
    }

    private function agente(Request $request): EquipoAgente
    {
        return $request->attributes->get('agente');
    }

    private function equipo(EquipoAgente $agente): array
    {
        $equipo = $agente->equipo()->with('empresa:id,nombre')->first();

        return [
            'id'           => $equipo->id,
            'nombre'       => $equipo->nombre,
            'numero_serie' => $equipo->numero_serie,
            'empresa'      => $equipo->empresa?->nombre,
        ];
    }
}
