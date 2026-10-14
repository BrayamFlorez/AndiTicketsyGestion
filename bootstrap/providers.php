<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    // Módulo de auditoría documental — 100% independiente, ver app/Providers/AuditingServiceProvider.php
    App\Providers\AuditingServiceProvider::class,
    App\Providers\AgenteServiceProvider::class,
];
