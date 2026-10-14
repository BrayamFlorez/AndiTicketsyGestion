<?php

namespace App\Http\Controllers;

use App\Http\Requests\Proyectos\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('clientes.gestionar'), 403);

        $filtros = $request->only(['q', 'activo']);

        $clientes = Cliente::query()
            ->buscar($filtros['q'] ?? null)
            ->when(($filtros['activo'] ?? '') !== '', fn ($q) => $q->where('activo', (bool) $filtros['activo']))
            ->withCount('proyectos')
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'filtros'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('clientes.gestionar'), 403);

        return view('clientes.create', ['cliente' => new Cliente(['activo' => true, 'tipo_documento' => 'nit'])]);
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        $cliente = Cliente::create(array_merge($request->validated(), [
            'creado_por' => $request->user()->id,
        ]));

        // Si se creó desde el formulario de proyecto, volver ahí con el cliente preseleccionado
        if ($request->filled('volver_a_proyecto')) {
            return redirect()
                ->route('proyectos.create', ['cliente_id' => $cliente->id])
                ->with('exito', "Cliente {$cliente->nombre} creado.");
        }

        return redirect()->route('clientes.index')->with('exito', "Cliente {$cliente->nombre} creado.");
    }

    public function edit(Request $request, Cliente $cliente): View
    {
        abort_unless($request->user()->can('clientes.gestionar'), 403);

        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')->with('exito', "Cliente {$cliente->nombre} actualizado.");
    }

    public function destroy(Request $request, Cliente $cliente): RedirectResponse
    {
        abort_unless($request->user()->can('clientes.gestionar'), 403);

        if ($cliente->proyectos()->withTrashed()->exists()) {
            return back()->with('error', 'El cliente tiene proyectos asociados. Desactivalo en lugar de eliminarlo.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('exito', 'Cliente eliminado.');
    }
}
