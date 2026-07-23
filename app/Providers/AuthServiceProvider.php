<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\WorkflowInstance::class => \App\Policies\WorkflowInstancePolicy::class,
    ];

    public function boot(): void
    {
        // $this->registerPolicies(); // Si usas Laravel 10 o inferior, descomenta esto
    }
}