<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Registra la API del agente de inventario.
 * Agregar en bootstrap/providers.php: App\Providers\AgenteServiceProvider::class
 */
class AgenteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Instalación: por IP, antes de validar la llave (frena pruebas con llaves inventadas)
        RateLimiter::for('agente-registro', fn (Request $request) => Limit::perMinute(config('agente.intentos_registro_por_minuto'))
            ->by('agente-reg|' . $request->ip())
            ->response(fn () => response()->json([
                'codigo'  => 'demasiados_intentos',
                'mensaje' => 'Demasiados intentos. Espere un minuto y vuelva a intentar.',
            ], 429)));

        // Reportes: por token, así no se afectan PCs que comparten IP
        RateLimiter::for('agente-reporte', fn (Request $request) => Limit::perMinute(10)
            ->by('agente-rep|' . sha1((string) $request->bearerToken())));

        if (! $this->app->routesAreCached()) {
            Route::prefix('api')->middleware('api')->group(base_path('routes/agente.php'));
        }
    }
}
