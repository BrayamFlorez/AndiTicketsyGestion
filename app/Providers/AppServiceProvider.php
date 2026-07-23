<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <-- Importar el Gate
use App\Models\WorkflowInstance;
use App\Policies\WorkflowInstancePolicy;

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
    }
}