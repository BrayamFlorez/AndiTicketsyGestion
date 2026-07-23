<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(Request $request): View
    {
        $filtro = $request->query('filtro', 'todas'); // todas | no_leidas | leidas

        $query = $request->user()->notifications();

        $query = match ($filtro) {
            'no_leidas' => $query->whereNull('read_at'),
            'leidas'    => $query->whereNotNull('read_at'),
            default     => $query,
        };

        $notificaciones = $query->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('notificaciones.index', compact('notificaciones', 'filtro'));
    }

    public function marcarLeida(Request $request, string $id): RedirectResponse
    {
        $notif = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notif->markAsRead();

        // Si la notificación tiene URL, redirigimos allí; si no, volvemos al listado.
        $url = $notif->data['url'] ?? route('notificaciones.index');

        return redirect($url);
    }

    public function marcarTodasLeidas(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('exito', 'Todas las notificaciones se marcaron como leídas.');
    }
}