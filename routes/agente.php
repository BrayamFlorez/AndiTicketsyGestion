<?php

/*
|--------------------------------------------------------------------------
| API del agente de inventario
|--------------------------------------------------------------------------
| La carga AgenteServiceProvider con prefijo /api y el grupo "api".
| No necesita Sanctum: la instalación usa la llave de la empresa y luego el PC su propio token.
*/

use App\Http\Controllers\Api\AgenteController;
use App\Http\Middleware\AutenticarAgente;
use App\Http\Middleware\AutenticarLlaveEmpresa;
use Illuminate\Support\Facades\Route;

Route::prefix('agente/v1')->name('api.agente.')->controller(AgenteController::class)->group(function () {

    // Instalación: llave de la empresa
    Route::middleware(['throttle:agente-registro', AutenticarLlaveEmpresa::class])->group(function () {
        Route::post('registro/verificar', 'verificar')->name('verificar');
        Route::post('registro', 'registrar')->name('registrar');
    });

    // PC ya instalado: su propio token
    Route::middleware([AutenticarAgente::class, 'throttle:agente-reporte'])->group(function () {
        Route::post('reporte', 'reporte')->name('reporte');
        Route::get('estado', 'estado')->name('estado');
        Route::post('desvincular', 'desvincular')->name('desvincular');
    });
});
