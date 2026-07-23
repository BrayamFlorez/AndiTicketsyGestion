<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Equipo;
use App\Models\Sede;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        $partial = match (true) {
            $user->hasRole('super-admin') => 'super_admin',
            $user->hasRole('admin')       => 'admin',
            $user->hasRole('tecnico')     => 'tecnico',
            default                       => 'usuario',
        };

        $data = $this->{'datos' . ucfirst(str_replace('_', '', $partial))}($user);

        return view('dashboard.index', array_merge($data, compact('partial')));
    }

    // ── Datos por rol ─────────────────────────────────────────────────────

    private function datosSuperadmin(User $user): array
    {
        return array_merge(
            $this->kpisTicketsGlobales(),
            $this->kpisFlujosGlobales(),
            $this->kpisInfraestructura(),
            [
                'totalUsuarios'      => User::count(),
                'ticketsRecientes'   => Ticket::with(['solicitante', 'asignado', 'empresa'])
                    ->latest()->limit(8)->get(),
                'flujosRecientes'    => WorkflowInstance::with(['template:id,nombre', 'iniciador:id,name', 'etapaActual:id,nombre', 'documentType:id,nombre,icono'])
                    ->latest('iniciado_en')->limit(8)->get(),
                'flujosPorPlantilla' => WorkflowTemplate::activos()
                    ->withCount(['instances as instancias_abiertas' => fn ($q) => $q->abiertas()])
                    ->orderByDesc('instancias_abiertas')
                    ->limit(5)->get(),
            ]
        );
    }

    private function datosAdmin(User $user): array
    {
        return array_merge(
            $this->kpisTicketsGlobales(),
            $this->kpisFlujosGlobales(),
            [
                'totalEmpresas'          => Empresa::count(),
                'totalSedes'             => Sede::count(),
                'totalEquipos'           => Equipo::count(),
                'ticketsRecientes'       => Ticket::with(['solicitante', 'asignado', 'empresa'])
                    ->latest()->limit(8)->get(),
                'flujosRecientes'        => WorkflowInstance::with(['template:id,nombre', 'iniciador:id,name', 'etapaActual:id,nombre', 'documentType:id,nombre,icono'])
                    ->latest('iniciado_en')->limit(8)->get(),
                'misFlujosPendientes'    => WorkflowInstance::pendientesParaUsuario($user)->count(),
                'tecnicos' => User::tecnicos()->activos()->withCount([
                    'ticketsAsignados as activos' => fn ($q) => $q->whereIn('estado', ['asignado', 'en_progreso']),
                ])->get(),
            ]
        );
    }

    private function datosTecnico(User $user): array
    {
        return [
            // Tickets propios (existente)
            'misTickets'       => Ticket::asignadosA($user->id)
                ->with(['empresa', 'sede', 'solicitante'])
                ->whereIn('estado', ['asignado', 'en_progreso'])
                ->orderByRaw("FIELD(prioridad, 'critica', 'alta', 'media', 'baja')")
                ->get(),
            'ticketsPorEstado' => Ticket::asignadosA($user->id)
                ->selectRaw('estado, count(*) as total')
                ->groupBy('estado')->pluck('total', 'estado'),
            'resueltos'        => Ticket::asignadosA($user->id)->where('estado', 'resuelto')->count(),
            'totalAsignados'   => Ticket::asignadosA($user->id)->count(),

            // Flujos donde participa
            'flujosPendientes'       => WorkflowInstance::pendientesParaUsuario($user)
                ->with(['template:id,nombre', 'iniciador:id,name', 'etapaActual:id,nombre', 'documentType:id,nombre,icono'])
                ->orderBy('iniciado_en')
                ->limit(6)->get(),
            'flujosPendientesCount'  => WorkflowInstance::pendientesParaUsuario($user)->count(),
            'flujosParticipandoCount' => WorkflowInstance::abiertas()
                ->whereHas('steps.assignments', fn ($q) => $q->where('user_id', $user->id))
                ->count(),
        ];
    }

    private function datosUsuario(User $user): array
    {
        return [
            // Tickets propios (existente)
            'misTickets'       => Ticket::delSolicitante($user->id)
                ->with(['asignado'])->latest()->limit(10)->get(),
            'ticketsPorEstado' => Ticket::delSolicitante($user->id)
                ->selectRaw('estado, count(*) as total')
                ->groupBy('estado')->pluck('total', 'estado'),
            'totalMisTickets'  => Ticket::delSolicitante($user->id)->count(),
            'abiertos'         => Ticket::delSolicitante($user->id)
                ->whereIn('estado', ['abierto', 'asignado', 'en_progreso'])->count(),

            // Flujos iniciados por el usuario
            'misFlujos' => WorkflowInstance::where('iniciado_por', $user->id)
                ->with(['template:id,nombre', 'etapaActual:id,nombre', 'documentType:id,nombre,icono'])
                ->latest('iniciado_en')->limit(6)->get(),
            'misFlujosAbiertos'    => WorkflowInstance::where('iniciado_por', $user->id)->abiertas()->count(),
            'misFlujosCorreccion'  => WorkflowInstance::where('iniciado_por', $user->id)
                ->where('estado', WorkflowInstance::ESTADO_REQUIERE_CORRECCION)->count(),
            'misFlujosAprobados'   => WorkflowInstance::where('iniciado_por', $user->id)
                ->where('estado', WorkflowInstance::ESTADO_APROBADO)->count(),
        ];
    }

    // ── Helpers de KPIs por dominio ───────────────────────────────────────

    private function kpisTicketsGlobales(): array
    {
        return [
            'totalTickets'     => Ticket::count(),
            'ticketsPorEstado' => Ticket::selectRaw('estado, count(*) as total')
                ->groupBy('estado')->pluck('total', 'estado'),
            'sinAsignar'       => Ticket::pendientesDeAsignacion()->count(),
        ];
    }

    private function kpisFlujosGlobales(): array
    {
        return [
            'flujosAbiertos'    => WorkflowInstance::abiertas()->count(),
            'flujosCorreccion'  => WorkflowInstance::where('estado', WorkflowInstance::ESTADO_REQUIERE_CORRECCION)->count(),
            'flujosCerradosMes' => WorkflowInstance::where('estado', WorkflowInstance::ESTADO_APROBADO)
                ->whereYear('cerrado_en', now()->year)
                ->whereMonth('cerrado_en', now()->month)
                ->count(),
            'flujosPorEstado'   => WorkflowInstance::selectRaw('estado, count(*) as total')
                ->groupBy('estado')->pluck('total', 'estado'),
        ];
    }

    private function kpisInfraestructura(): array
    {
        return [
            'totalEmpresas'    => Empresa::count(),
            'totalSedes'       => Sede::count(),
            'totalEquipos'     => Equipo::count(),
            'equiposPorEstado' => Equipo::selectRaw('estado, count(*) as total')
                ->groupBy('estado')->pluck('total', 'estado'),
        ];
    }
}