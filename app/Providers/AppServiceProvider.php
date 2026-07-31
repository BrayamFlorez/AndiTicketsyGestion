<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Importar el Gate
use App\Models\WorkflowInstance;
use App\Policies\WorkflowInstancePolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registrar la política manualmente
        Gate::policy(WorkflowInstance::class, WorkflowInstancePolicy::class);
        // Contador de flujos pendientes en el sidebar (cache TTL 60s por usuario)
        View::composer('layouts.partials.sidebar', function ($view) {
            $user = auth()->user();

            $count = 0;
            if ($user) {
                $count = Cache::remember(
                    "sidebar.flujos.pendientes.user.{$user->id}",
                    now()->addSeconds(60),
                    fn () => WorkflowInstance::pendientesParaUsuario($user)->count()
                );
            }

            $view->with('flujosPendientesSidebar', $count);
        });
    }
}