<?php

namespace App\Http\Controllers;

use App\Models\MantenimientoPreventivo;
use App\Models\Equipo;
use App\Http\Requests\MantenimientoPreventivoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MantenimientoPreventivoController extends Controller
{

    public function create(Equipo $equipo): View
    {
        // Pasamos el equipo a la vista para saber a quién le haremos mantenimiento
        $mantenimiento = new MantenimientoPreventivo();
        return view('mpreventivo.create', compact('equipo', 'mantenimiento'));
    }

    /**
     * Guarda el mantenimiento en la base de datos.
     */
    public function store(MantenimientoPreventivoRequest $request, Equipo $equipo): RedirectResponse
    {
        $data = $request->validated();

        // 1. Vinculamos el equipo y el técnico que realiza la acción
        $data['equipo_id']  = $equipo->id;
        $data['tecnico_id'] = Auth::id();

        // 2. Manejo de Checkboxes (HTML no envía los desmarcados, forzamos el booleano)
        $this->procesarCheckboxes($request, $data);

        // 3. Manejo de Archivos (Firma y Evidencia)
        if ($request->hasFile('firma_cliente_path')) {
            $data['firma_cliente_path'] = $request->file('firma_cliente_path')->store('mantenimientos/firmas', 'public');
        }
        if ($request->hasFile('evidencia_fotografica_path')) {
            $data['evidencia_fotografica_path'] = $request->file('evidencia_fotografica_path')->store('mantenimientos/evidencias', 'public');
        }

        MantenimientoPreventivo::create($data);

        // Retornamos a la vista del equipo (su hoja de vida)
        return redirect()->route('equipos.show', $equipo)
            ->with('success', 'Mantenimiento preventivo registrado exitosamente.');
    }
    

    /**
     * Muestra el detalle del mantenimiento.
     */
    public function show(MantenimientoPreventivo $mantenimiento): View
    {
        $mantenimiento->load(['equipo', 'tecnico']);
        return view('mpreventivo.show', compact('mantenimiento'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(MantenimientoPreventivo $mantenimiento): View
    {
        $equipo = $mantenimiento->equipo;
        return view('mpreventivo.edit', compact('mantenimiento', 'equipo'));
    }

    /**
     * Actualiza el mantenimiento.
     */
    public function update(MantenimientoPreventivoRequest $request, MantenimientoPreventivo $mantenimiento): RedirectResponse
    {
        $data = $request->validated();

        $this->procesarCheckboxes($request, $data);

        // Reemplazo de Archivos
        if ($request->hasFile('firma_cliente_path')) {
            if ($mantenimiento->firma_cliente_path) Storage::disk('public')->delete($mantenimiento->firma_cliente_path);
            $data['firma_cliente_path'] = $request->file('firma_cliente_path')->store('mantenimientos/firmas', 'public');
        }

        if ($request->hasFile('evidencia_fotografica_path')) {
            if ($mantenimiento->evidencia_fotografica_path) Storage::disk('public')->delete($mantenimiento->evidencia_fotografica_path);
            $data['evidencia_fotografica_path'] = $request->file('evidencia_fotografica_path')->store('mantenimientos/evidencias', 'public');
        }

        $mantenimiento->update($data);

        return redirect()->route('equipos.show', $mantenimiento->equipo_id)
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    /**
     * Elimina el registro.
     */
    public function destroy(MantenimientoPreventivo $mantenimiento): RedirectResponse
    {
        $equipo_id = $mantenimiento->equipo_id;

        // Limpiar archivos físicos antes de borrar de BD
        if ($mantenimiento->firma_cliente_path) Storage::disk('public')->delete($mantenimiento->firma_cliente_path);
        if ($mantenimiento->evidencia_fotografica_path) Storage::disk('public')->delete($mantenimiento->evidencia_fotografica_path);

        $mantenimiento->delete();

        return redirect()->route('equipos.show', $equipo_id)
            ->with('success', 'Registro de mantenimiento eliminado.');
    }

    /**
     * Función Helper para limpiar el código de los checkboxes
     */
    private function procesarCheckboxes(MantenimientoPreventivoRequest $request, array &$data): void
    {
        $camposBooleanos = [
            'hw_limpieza_interna', 'hw_limpieza_externa', 'hw_cambio_pasta_termica',
            'hw_revision_ventiladores', 'hw_verificacion_cables', 'sw_limpieza_temporales',
            'sw_actualizacion_so', 'sw_revision_antivirus', 'sw_revision_disco_duro',
            'sw_desinstalacion_basura'
        ];

        foreach ($camposBooleanos as $campo) {
            $data[$campo] = $request->boolean($campo);
        }
    }
}