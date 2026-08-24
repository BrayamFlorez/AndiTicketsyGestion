<?php

namespace App\Http\Controllers;

use App\Actions\Flujos\ActuarEnAsignacionAction;
use App\Actions\Flujos\CancelarFlujoAction;
use App\Actions\Flujos\EnviarCorreccionAction;
use App\Actions\Flujos\IniciarFlujoAction;
use App\Actions\Flujos\ReabrirFlujoAction;
use App\Http\Requests\Flujos\ActuarEnAsignacionRequest;
use App\Http\Requests\Flujos\StoreWorkflowInstanceRequest;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\WorkflowStageActor;
use App\Models\WorkflowStepAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Actions\Flujos\SubirAdjuntoAction;
use App\Http\Requests\Flujos\StoreWorkflowAttachmentRequest;
use App\Models\WorkflowAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\WorkflowTemplate;
use App\Events\Auditing\DocumentoVisualizado;
use App\Events\Auditing\AdjuntoDescargado;

class WorkflowInstanceController extends Controller
{
    use AuthorizesRequests;
    /**
     * Bandeja unificada con tabs. Cada tab arma su propia query en base a
     * los permisos del usuario para no filtrar resultados en memoria.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tabsDisponibles = $this->tabsPermitidos($user);

        $tabActivo = $request->query('tab', array_key_first($tabsDisponibles));
        if (! isset($tabsDisponibles[$tabActivo])) {
            $tabActivo = array_key_first($tabsDisponibles);
        }

        $filtros = $request->only(['q', 'template_id', 'document_type_id', 'desde', 'hasta']);

        $instancias = $this->queryPorTab($user, $tabActivo, $filtros)
            ->orderByDesc('iniciado_en')
            ->paginate(15)
            ->withQueryString();

        $plantillas     = WorkflowTemplate::orderBy('nombre')->get(['id', 'nombre']);
        $tiposDocumento = DocumentType::orderBy('nombre')->get(['id', 'nombre']);

        return view('flujos.instancias.index', compact(
            'instancias', 'tabActivo', 'tabsDisponibles',
            'filtros', 'plantillas', 'tiposDocumento'
        ));
    }

    private function queryPorTab(User $user, string $tab, array $filtros = [])
    {
        $base = WorkflowInstance::query()
            ->with([
                'template:id,nombre',
                'iniciador:id,name',
                'etapaActual:id,nombre',
                'documentType:id,nombre,icono',
                'currentStep.stage:id,nombre,sla_horas',
            ])
            ->withCount('attachments')
            // ── Filtros comunes ──
            ->when($filtros['q'] ?? null, function ($q, $texto) {
                $q->where(function ($sub) use ($texto) {
                    $sub->where('codigo',      'like', "%{$texto}%")
                        ->orWhere('titulo',      'like', "%{$texto}%")
                        ->orWhere('descripcion', 'like', "%{$texto}%")
                        ->orWhereHas('iniciador',   fn ($iq) => $iq->where('name', 'like', "%{$texto}%"))
                        ->orWhereHas('attachments', fn ($aq) => $aq->where('nombre_original', 'like', "%{$texto}%"));
                });
            })
            ->when($filtros['template_id']      ?? null, fn ($q, $v) => $q->where('workflow_template_id', $v))
            ->when($filtros['document_type_id'] ?? null, fn ($q, $v) => $q->where('document_type_id', $v))
            ->when($filtros['desde']            ?? null, fn ($q, $v) => $q->whereDate('iniciado_en', '>=', $v))
            ->when($filtros['hasta']            ?? null, fn ($q, $v) => $q->whereDate('iniciado_en', '<=', $v));

        $idsRolesUsuario = $user->roles->pluck('id');

        return match ($tab) {
            'pendientes' => $base
                ->abiertas()
                ->whereHas('steps', function ($q) use ($user, $idsRolesUsuario) {
                    $q->where('estado', WorkflowInstanceStep::ESTADO_EN_CURSO)
                      ->whereHas('assignments', function ($aq) use ($user, $idsRolesUsuario) {
                          $aq->where('estado', WorkflowStepAssignment::ESTADO_PENDIENTE)
                             ->where(function ($sq) use ($user, $idsRolesUsuario) {
                                 $sq->where('user_id', $user->id)
                                    ->orWhere(function ($rq) use ($idsRolesUsuario) {
                                        $rq->whereNull('user_id')
                                           ->whereHas('stageActor', function ($actorQ) use ($idsRolesUsuario) {
                                               $actorQ->where('actor_type', WorkflowStageActor::TIPO_ROL)
                                                      ->whereIn('actor_id', $idsRolesUsuario);
                                           });
                                    });
                             });
                      });
                }),

            'iniciados'    => $base->where('iniciado_por', $user->id)->abiertas(),
            'correcciones' => $base->where('iniciado_por', $user->id)
                                   ->where('estado', WorkflowInstance::ESTADO_REQUIERE_CORRECCION),
            'todos'        => $base->abiertas(),
            'cerrados'     => $base->whereIn('estado', [
                WorkflowInstance::ESTADO_APROBADO,
                WorkflowInstance::ESTADO_CANCELADO,
                WorkflowInstance::ESTADO_ARCHIVADO,
            ]),

            default => $base,
        };
    }

    public function create(): View
    {
        $tipos = DocumentType::activos()
            ->whereNotNull('workflow_template_id')
            ->whereHas('template', fn ($q) => $q->activos())
            ->with(['template:id,nombre', 'template.stages:id,workflow_template_id,nombre,orden,modo'])
            ->orderBy('nombre')
            ->get();

        return view('flujos.instancias.create', compact('tipos'));
    }

    public function store(StoreWorkflowInstanceRequest $request, IniciarFlujoAction $accion): RedirectResponse
    {
        $datos = $request->validated();

        $tipo = DocumentType::with('template')->findOrFail($datos['document_type_id']);

        $instancia = $accion->ejecutar($tipo->template, $request->user(), [
            'titulo'           => $datos['titulo'],
            'descripcion'      => $datos['descripcion'] ?? null,
            'document_type_id' => $tipo->id,
        ]);

        return redirect()
            ->route('flujos.instancias.show', $instancia)
            ->with('exito', "Flujo iniciado con código {$instancia->codigo}.");
    }

    public function show(WorkflowInstance $instancia): View
    {
        $this->authorize('view', $instancia);
        $user = auth()->user();

        // Auditoría: registra quién visualizó el documento y desde qué IP.
        // Ver App\Listeners\Auditing\DocumentAuditSubscriber.
        event(new DocumentoVisualizado($instancia, $user));

        $instancia->load([
            'template:id,nombre',
            'documentType:id,nombre,icono',
            'iniciador:id,name,email',
            'etapaActual:id,nombre',
            'steps' => fn ($q) => $q->orderBy('id'),
            'steps.stage:id,nombre,orden,modo,politica_rechazo,requiere_adjunto',
            'steps.assignments' => fn ($q) => $q->orderBy('id'),
            'steps.assignments.stageActor',
            'steps.assignments.user:id,name,email',
            // Adjuntos POR ETAPA (van embebidos en la línea de tiempo)
            'steps.attachments' => fn ($q) => $q->orderByDesc('created_at'),
            'steps.attachments.uploader:id,name',
            // Adjuntos del EXPEDIENTE (trámite-level, workflow_instance_step_id IS NULL)
            'attachments' => fn ($q) => $q->whereNull('workflow_instance_step_id')->orderByDesc('created_at'),
            'attachments.uploader:id,name',
        ]);

        $stepActual = $instancia->steps
            ->firstWhere('estado', WorkflowInstanceStep::ESTADO_EN_CURSO);

        $misAsignacionesPendientes = $this->asignacionesActuables($stepActual, $user);

        $this->hidratarEtiquetasActores($instancia);

        $puede = [
            'actuar'              => $misAsignacionesPendientes->isNotEmpty(),
            'corregir'            => $user->can('corregir', $instancia),
            'desistir'            => $user->can('desistir', $instancia),
            'cancelar'            => $user->can('cancelar', $instancia),
            'reabrir'             => $user->can('reabrir', $instancia),
            'adjuntar_expediente' => $user->can('subirAdjuntoInstancia', $instancia),
            'adjuntar_step'       => $stepActual && $user->can('subirAdjuntoStep', [$instancia, $stepActual]),
        ];

        return view('flujos.instancias.show', compact(
            'instancia', 'stepActual', 'misAsignacionesPendientes', 'puede'
        ));
    }

    public function actuar(
        ActuarEnAsignacionRequest $request,
        WorkflowInstance $instancia,
        WorkflowStepAssignment $assignment,
        ActuarEnAsignacionAction $accion,
    ): RedirectResponse {

        // Validar que la asignación efectivamente pertenezca a esta instancia
        abort_if(
            $assignment->step->workflow_instance_id !== $instancia->id,
            404,
            'Asignación fuera de contexto.'
        );

        $accion->ejecutar(
            $assignment,
            $request->user(),
            $request->validated('accion'),
            $request->validated('comentario')
        );

        return back()->with('exito', 'Decisión registrada correctamente.');
    }

    public function corregir(WorkflowInstance $instancia, EnviarCorreccionAction $accion): RedirectResponse
    {
        $this->authorize('corregir', $instancia);

        $accion->ejecutar($instancia, auth()->user());

        return back()->with('exito', 'Flujo reenviado tras corrección.');
    }

    public function desistir(Request $request, WorkflowInstance $instancia, CancelarFlujoAction $accion): RedirectResponse
    {
        $this->authorize('desistir', $instancia);

        $datos = $request->validate([
            'motivo' => ['nullable', 'string', 'max:1000'],
        ]);

        $accion->ejecutar($instancia, $request->user(), $datos['motivo'] ?? 'Desistido por el iniciador.');

        return redirect()
            ->route('flujos.instancias.index')
            ->with('exito', 'Flujo cerrado.');
    }

    public function cancelar(Request $request, WorkflowInstance $instancia, CancelarFlujoAction $accion): RedirectResponse
    {
        $datos = $request->validate([
            'motivo' => ['required', 'string', 'max:1000'],
        ]);

        $accion->ejecutar($instancia, $request->user(), $datos['motivo']);

        return back()->with('exito', 'Flujo cancelado.');
    }

    public function reabrir(WorkflowInstance $instancia, ReabrirFlujoAction $accion): RedirectResponse
    {
        $accion->ejecutar($instancia);

        return back()->with('exito', 'Flujo reabierto.');
    }

    // ─── Helpers privados ────────────────────────────────────────────────

    private function tabsPermitidos(User $user): array
    {
        $tabs = [];

        if ($user->can('flujos.ver-participando') || $user->can('flujos.ver-todos')) {
            $tabs['pendientes'] = ['label' => 'Mis Pendientes', 'icono' => 'fa-user-clock'];
        }

        if ($user->can('flujos.iniciar')) {
            $tabs['iniciados']    = ['label' => 'Iniciados por mí',      'icono' => 'fa-user-check'];
            $tabs['correcciones'] = ['label' => 'Requieren corrección',  'icono' => 'fa-exclamation-triangle'];
        }

        if ($user->can('flujos.ver-todos')) {
            $tabs['todos'] = ['label' => 'Todos los flujos', 'icono' => 'fa-globe'];
        }

        $tabs['cerrados'] = ['label' => 'Cerrados', 'icono' => 'fa-check-double'];

        return $tabs;
    }

    /**
     * Devuelve la subcolección de asignaciones del step actual sobre las
     * que este usuario puede actuar (por asignación directa o por rol).
     */
    private function asignacionesActuables(?WorkflowInstanceStep $stepActual, User $user)
    {
        if (! $stepActual) {
            return collect();
        }

        $idsRolesUsuario = $user->roles->pluck('id');

        return $stepActual->assignments->filter(function ($a) use ($user, $idsRolesUsuario) {
            if ($a->estado !== WorkflowStepAssignment::ESTADO_PENDIENTE) {
                return false;
            }

            if ($a->user_id === $user->id) {
                return true;
            }

            return $a->user_id === null
                && $a->stageActor?->actor_type === WorkflowStageActor::TIPO_ROL
                && $idsRolesUsuario->contains($a->stageActor->actor_id);
        })->values();
    }

    /**
     * Añade dinámicamente al objeto WorkflowStageActor una propiedad
     * `label_resuelto` para que la vista lo renderice sin resolver otra vez.
     */
    private function hidratarEtiquetasActores(WorkflowInstance $instancia): void
    {
        $instancia->steps->each(function ($step) {
            $step->assignments->each(function ($assignment) {
                $actor = $assignment->stageActor;

                if (! $actor) {
                    $assignment->label_resuelto = 'Actor eliminado';
                    return;
                }

                $resuelto = $actor->resolverActor();
                $assignment->label_resuelto = $actor->esRol()
                    ? 'Rol: '.($resuelto?->name ?? '—')
                    : 'Usuario: '.($resuelto?->name ?? '—');
            });
        });
    }

    public function subirAdjunto(
        StoreWorkflowAttachmentRequest $request,
        WorkflowInstance $instancia,
        SubirAdjuntoAction $accion,
    ): RedirectResponse {

        $stepId = $request->validated('workflow_instance_step_id');
        $step = $stepId ? WorkflowInstanceStep::find($stepId) : null;

        // Autorización según destino
        if ($step) {
            $this->authorize('subirAdjuntoStep', [$instancia, $step]);
        } else {
            $this->authorize('subirAdjuntoInstancia', $instancia);
        }

        $accion->ejecutar(
            $instancia,
            $request->user(),
            $request->file('archivo'),
            $step,
        );

        return back()->with('exito', 'Archivo adjuntado correctamente.');
    }

    public function descargarAdjunto(
        WorkflowInstance $instancia,
        WorkflowAttachment $attachment,
    ): StreamedResponse {

        abort_if(
            $attachment->workflow_instance_id !== $instancia->id,
            404,
            'Adjunto fuera de contexto.'
        );

        // Reutiliza la policy de "ver instancia" — mismo criterio de acceso
        $this->authorize('view', $instancia);

        abort_unless(
            Storage::disk('private')->exists($attachment->ruta),
            404,
            'Archivo no encontrado en el almacenamiento.'
        );

        // Auditoría: registra quién descargó el adjunto y desde qué IP.
        // Ver App\Listeners\Auditing\DocumentAuditSubscriber.
        event(new AdjuntoDescargado($instancia, $attachment, auth()->user()));

        return Storage::disk('private')->download(
            $attachment->ruta,
            $attachment->nombre_original,
            ['Content-Type' => $attachment->mime_type],
        );
    }

    public function eliminarAdjunto(
        WorkflowInstance $instancia,
        WorkflowAttachment $attachment,
    ): RedirectResponse {

        abort_if(
            $attachment->workflow_instance_id !== $instancia->id,
            404,
        );

        $this->authorize('eliminarAdjunto', $attachment);

        Storage::disk('private')->delete($attachment->ruta);
        $attachment->delete();

        return back()->with('exito', 'Adjunto eliminado.');
    }    
}