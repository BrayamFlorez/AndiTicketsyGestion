<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flujos\StoreDocumentTypeRequest;
use App\Http\Requests\Flujos\UpdateDocumentTypeRequest;
use App\Models\DocumentType;
use App\Models\WorkflowTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    /**
     * Listado paginado con filtros por texto y estado.
     */
    public function index(Request $request): View
    {
        $filtros = $request->only(['q', 'activo']);

        $tipos = DocumentType::query()
            ->with('template:id,nombre')
            ->when($filtros['q'] ?? null, function ($query, string $texto) {
                $query->where(function ($q) use ($texto) {
                    $q->where('nombre', 'like', "%{$texto}%")
                      ->orWhere('slug', 'like', "%{$texto}%");
                });
            })
            ->when(
                array_key_exists('activo', $filtros) && $filtros['activo'] !== '',
                fn ($q) => $q->where('activo', (bool) $filtros['activo'])
            )
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('flujos.tipos-documento.index', compact('tipos', 'filtros'));
    }

    public function create(): View
    {
        return view('flujos.tipos-documento.create', [
            'tipo'       => new DocumentType(['activo' => true, 'icono' => 'fa-file-alt']),
            'plantillas' => $this->plantillasDisponibles(),
        ]);
    }

    public function store(StoreDocumentTypeRequest $request): RedirectResponse
    {
        DocumentType::create($request->validated());

        return redirect()
            ->route('flujos.tipos-documento.index')
            ->with('exito', 'Tipo de documento creado correctamente.');
    }

    public function edit(DocumentType $tipoDocumento): View
    {
        return view('flujos.tipos-documento.edit', [
            'tipo'       => $tipoDocumento,
            'plantillas' => $this->plantillasDisponibles(),
        ]);
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $tipoDocumento): RedirectResponse
    {
        $tipoDocumento->update($request->validated());

        return redirect()
            ->route('flujos.tipos-documento.index')
            ->with('exito', 'Tipo de documento actualizado.');
    }

    public function destroy(DocumentType $tipoDocumento): RedirectResponse
    {
        $tipoDocumento->delete();

        return redirect()
            ->route('flujos.tipos-documento.index')
            ->with('exito', 'Tipo de documento eliminado.');
    }

    /**
     * Colección de plantillas activas para el select del formulario.
     */
    private function plantillasDisponibles()
    {
        return WorkflowTemplate::activos()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }
}