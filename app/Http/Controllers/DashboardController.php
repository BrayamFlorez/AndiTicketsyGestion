<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Determina qué partial renderizar según el primer rol asignado.
        // El orden importa: si alguien tiene varios roles, el más alto manda.
        $partial = match (true) {
            $user->hasRole('super-admin') => 'super_admin',
            $user->hasRole('admin')       => 'admin',
            $user->hasRole('tecnico')     => 'tecnico',
            default                       => 'usuario',
        };

        // Carga solo los datos que ese rol necesita
        $data = $this->{'datos' . ucfirst(str_replace('_', '', $partial))}($user);

        return view('dashboard.index', array_merge($data, compact('partial')));
    }

    // ── Datos por rol ─────────────────────────────────────────────────────

    private function datosSuperadmin(User $user): array
    {
        return [
            'totalTickets'    => Ticket::count(),
            'ticketsPorEstado' => Ticket::selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'totalUsuarios'   => User::count(),
            'totalEmpresas'   => Empresa::count(),
            'ticketsRecientes' => Ticket::with(['solicitante', 'asignado', 'empresa'])
                ->latest()
                ->limit(10)
                ->get(),
            'sinAsignar'      => Ticket::pendientesDeAsignacion()->count(),
        ];
    }

    private function datosAdmin(User $user): array
    {
        return [
            'totalTickets'     => Ticket::count(),
            'ticketsPorEstado' => Ticket::selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'sinAsignar'       => Ticket::pendientesDeAsignacion()->count(),
            'ticketsRecientes' => Ticket::with(['solicitante', 'asignado', 'empresa'])
                ->latest()
                ->limit(10)
                ->get(),
            'tecnicos'         => User::tecnicos()->activos()->withCount([
                'ticketsAsignados as activos' => fn($q) => $q->whereIn('estado', ['asignado', 'en_progreso']),
            ])->get(),
        ];
    }

    private function datosTecnico(User $user): array
    {
        return [
            'misTickets'       => Ticket::asignadosA($user->id)
                ->with(['empresa', 'sede', 'solicitante'])
                ->whereIn('estado', ['asignado', 'en_progreso'])
                ->orderByRaw("FIELD(prioridad, 'critica', 'alta', 'media', 'baja')")
                ->get(),
            'ticketsPorEstado' => Ticket::asignadosA($user->id)
                ->selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'resueltos'        => Ticket::asignadosA($user->id)
                ->where('estado', 'resuelto')
                ->count(),
            'totalAsignados'   => Ticket::asignadosA($user->id)->count(),
        ];
    }

    private function datosUsuario(User $user): array
    {
        return [
            'misTickets'       => Ticket::delSolicitante($user->id)
                ->with(['asignado'])
                ->latest()
                ->limit(10)
                ->get(),
            'ticketsPorEstado' => Ticket::delSolicitante($user->id)
                ->selectRaw('estado, count(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado'),
            'totalMisTickets'  => Ticket::delSolicitante($user->id)->count(),
            'abiertos'         => Ticket::delSolicitante($user->id)
                ->whereIn('estado', ['abierto', 'asignado', 'en_progreso'])
                ->count(),
        ];
    }
}