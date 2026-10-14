<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\EquipoAgente;
use App\Services\Agente\LlaveAgenteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Gestión del agente desde el sistema: token de instalación por empresa y revocación por equipo.
 */
class AgenteWebController extends Controller
{
    public function __construct(private readonly LlaveAgenteService $llaves) {}

    /** Genera o regenera la llave. La anterior deja de servir para instalar; los PCs instalados siguen. */
    public function generarLlave(Empresa $empresa): RedirectResponse
    {
        $regenerada = filled($empresa->agente_llave_hash);
        $this->llaves->generar($empresa);

        return back()->with('success', $regenerada
            ? 'Token regenerado. El anterior ya no sirve para instalar; los PCs instalados siguen reportando.'
            : 'Token de instalación generado.');
    }

    /** Sin llave nadie puede instalar el agente en esa empresa. Los PCs instalados siguen reportando. */
    public function revocarLlave(Empresa $empresa): RedirectResponse
    {
        $this->llaves->revocar($empresa);

        return back()->with('success', 'Token revocado. No se pueden instalar agentes nuevos en esta empresa.');
    }

    /** Revoca el agente del equipo: su token deja de funcionar y el PC deja de reportar */
    public function revocarAgente(Request $request, Equipo $equipo): RedirectResponse
    {
        abort_unless($equipo->puedeSerGestionadoPor($request->user()), 403,
            'No tienes cobertura para gestionar este equipo.');

        $agente = EquipoAgente::activos()->where('equipo_id', $equipo->id)->first();

        if (! $agente) {
            return back()->with('error', 'Este equipo no tiene un agente activo.');
        }

        $agente->forceFill([
            'revocado_at'  => now(),
            'revocado_por' => $request->user()->id,
        ])->save();

        return back()->with('success', "Agente de \"{$equipo->nombre}\" revocado. El PC dejará de actualizar el equipo.");
    }
}
