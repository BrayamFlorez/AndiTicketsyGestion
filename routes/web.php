<?php

use Illuminate\Support\Facades\Route;

// ── Controladores ────────────────────────────────────────────────────────
use App\Http\Controllers\ArchivoPrivadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentAuditLogController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\MantenimientoPreventivoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkflowInstanceController;
use App\Http\Controllers\WorkflowTemplateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuditoriaController;   

/*
|--------------------------------------------------------------------------
| Rutas Públicas y Generales
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('/motor-flujos', 'publico.motor-flujos')->name('publico.motor-flujos');
Route::view('/doc-flujos', 'publico.docs-flujos')->name('publico.doc-flujos');

// Cargar rutas de prueba temporalmente
require __DIR__ . '/test_routes.php';

// Notificaciones
Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
    Route::get('/', [NotificacionController::class, 'index'])->name('index');
    Route::post('/{id}/leer', [NotificacionController::class, 'marcarLeida'])->name('leer');
    Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('marcar-todas');
});

/*
|--------------------------------------------------------------------------
| Rutas Autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ── Dashboard y Perfil ──
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Usuarios y Roles ──
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:usuarios.ver')->name('index');
        Route::get('/create', [UserController::class, 'create'])->middleware('can:usuarios.crear')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('can:usuarios.crear')->name('store');
        Route::get('/{usuario}/edit', [UserController::class, 'edit'])->middleware('can:usuarios.editar')->name('edit');
        Route::put('/{usuario}', [UserController::class, 'update'])->middleware('can:usuarios.editar')->name('update');
        Route::delete('/{usuario}', [UserController::class, 'destroy'])->middleware('can:usuarios.eliminar')->name('destroy');
        Route::post('/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])->middleware('can:usuarios.editar')->name('toggle-activo');
    });
    Route::resource('roles', RoleController::class);
    Route::get(
        'roles/{role}/confirmar-eliminacion',
        [RoleController::class, 'confirmarEliminacion']
    )
        ->name('roles.confirmar-eliminacion');

    // ── Entidades Base ──
    Route::resource('empresas', EmpresaController::class)->middleware('can:empresas.gestionar');
    Route::resource('sedes', SedeController::class)->middleware('can:sedes.gestionar');

    // ── Equipos ──
    Route::prefix('equipos')->name('equipos.')->group(function () {
        Route::get('/', [EquipoController::class, 'index'])->middleware('can:equipos.ver')->name('index');
        Route::get('/create', [EquipoController::class, 'create'])->middleware('can:equipos.crear')->name('create');
        Route::post('/', [EquipoController::class, 'store'])->middleware('can:equipos.crear')->name('store');
        Route::get('/{equipo}/edit', [EquipoController::class, 'edit'])->middleware('can:equipos.editar')->name('edit');
        Route::put('/{equipo}', [EquipoController::class, 'update'])->middleware('can:equipos.editar')->name('update');
        Route::patch('/{equipo}/estado', [EquipoController::class, 'cambiarEstado'])->middleware('can:equipos.editar')->name('cambiar-estado');
        Route::delete('/{equipo}', [EquipoController::class, 'destroy'])->middleware('can:equipos.eliminar')->name('destroy');
        Route::get('/{equipo}', [EquipoController::class, 'show'])->middleware('can:equipos.ver')->name('show');
    });

    // ── Archivos Privados ──
    Route::get('/archivos-privados/{path}', [ArchivoPrivadoController::class, 'show'])
        ->where('path', '.*')
        ->name('archivos-privados.show');

    // ── Tickets ──
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->middleware('can:tickets.crear')->name('create');
        Route::post('/', [TicketController::class, 'store'])->middleware('can:tickets.crear')->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/asignar', [TicketController::class, 'asignar'])->middleware('can:tickets.asignar')->name('asignar');
        Route::post('/{ticket}/iniciar', [TicketController::class, 'iniciar'])->middleware('can:tickets.iniciar')->name('iniciar');
        Route::post('/{ticket}/resolver', [TicketController::class, 'resolver'])->middleware('can:tickets.resolver')->name('resolver');
        Route::post('/{ticket}/cerrar', [TicketController::class, 'cerrar'])->middleware('can:tickets.cerrar')->name('cerrar');
        Route::post('/{ticket}/cancelar', [TicketController::class, 'cancelar'])->middleware('can:tickets.cancelar')->name('cancelar');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->middleware('can:tickets.eliminar')->name('destroy');
    });

    // ── Flujos (Tipos, Plantillas e Instancias) ──
    Route::prefix('flujos')->name('flujos.')->group(function () {

        // 1. Tipos de documento
        Route::prefix('tipos-documento')->name('tipos-documento.')
            ->middleware('can:flujos.tipos-documento.gestionar')
            ->group(function () {
                Route::get('/', [DocumentTypeController::class, 'index'])->name('index');
                Route::get('/create', [DocumentTypeController::class, 'create'])->name('create');
                Route::post('/', [DocumentTypeController::class, 'store'])->name('store');
                Route::get('/{tipoDocumento}/edit', [DocumentTypeController::class, 'edit'])->name('edit');
                Route::put('/{tipoDocumento}', [DocumentTypeController::class, 'update'])->name('update');
                Route::delete('/{tipoDocumento}', [DocumentTypeController::class, 'destroy'])->name('destroy');
            });

        // 2. Plantillas de flujo
        Route::prefix('plantillas')->name('plantillas.')
            ->middleware('can:flujos.plantillas.gestionar')
            ->group(function () {
                Route::get('/', [WorkflowTemplateController::class, 'index'])->name('index');
                Route::get('/create', [WorkflowTemplateController::class, 'create'])->name('create');
                Route::post('/', [WorkflowTemplateController::class, 'store'])->name('store');
                Route::get('/{plantilla}/edit', [WorkflowTemplateController::class, 'edit'])->name('edit');
                Route::put('/{plantilla}', [WorkflowTemplateController::class, 'update'])->name('update');
                Route::delete('/{plantilla}', [WorkflowTemplateController::class, 'destroy'])->name('destroy');
                Route::post('/{plantilla}/toggle-activo', [WorkflowTemplateController::class, 'toggleActivo'])->name('toggle-activo');
                Route::get('/api/usuarios', [WorkflowTemplateController::class, 'buscarUsuarios'])->name('api.usuarios');
            });

        // 3. Instancias de flujo (¡Solucionado!)
        Route::prefix('instancias')->name('instancias.')->group(function () {
            Route::patch('/{instancia}/cabecera', [WorkflowInstanceController::class, 'actualizarCabecera'])
                ->name('cabecera');
            Route::post('/{instancia}/steps/{step}/comentarios', [WorkflowInstanceController::class, 'agregarComentarioStep'])
                ->name('steps.comentar');
            Route::patch(
                '/{instancia}/steps/{step}/codigo-referencia',
                [WorkflowInstanceController::class, 'actualizarCodigoReferenciaStep']
            )
                ->name('steps.codigo-referencia');
            Route::get('/', [WorkflowInstanceController::class, 'index'])->middleware('can:flujos.ver-participando')->name('index');
            Route::get('/create', [WorkflowInstanceController::class, 'create'])->middleware('can:flujos.iniciar')->name('create');
            Route::post('/', [WorkflowInstanceController::class, 'store'])->middleware('can:flujos.iniciar')->name('store');
            Route::get('/{instancia}', [WorkflowInstanceController::class, 'show'])->name('show');

            // Adjuntos
            Route::post('/{instancia}/adjuntos', [WorkflowInstanceController::class, 'subirAdjunto'])->name('adjuntos.subir');
            Route::get('/{instancia}/adjuntos/{attachment}/descargar', [WorkflowInstanceController::class, 'descargarAdjunto'])->name('adjuntos.descargar');
            Route::delete('/{instancia}/adjuntos/{attachment}', [WorkflowInstanceController::class, 'eliminarAdjunto'])->name('adjuntos.eliminar');

            // Asignaciones y Acciones
            Route::post('/{instancia}/asignaciones/{assignment}/actuar', [WorkflowInstanceController::class, 'actuar'])->name('asignaciones.actuar');
            Route::post('/{instancia}/corregir', [WorkflowInstanceController::class, 'corregir'])->name('corregir');
            Route::post('/{instancia}/desistir', [WorkflowInstanceController::class, 'desistir'])->name('desistir');

            Route::post('/{instancia}/cancelar', [WorkflowInstanceController::class, 'cancelar'])
                ->middleware('can:flujos.cancelar-cualquiera')->name('cancelar');
            Route::post('/{instancia}/reabrir', [WorkflowInstanceController::class, 'reabrir'])
                ->middleware('can:flujos.reabrir')->name('reabrir');
        });
    }); // Fin del grupo 'flujos'

    // Configuración de ajustes 
    Route::get(
        '/configuracion/ajustes',
        [SettingsController::class, 'index']
    )->name('configuracion.ajustes.index');

    Route::patch(
        '/configuracion/ajustes/{setting}',
        [SettingsController::class, 'update']
    )->name('configuracion.ajustes.update');
    // ── Auditoría documental (módulo independiente, de solo lectura) ──
    Route::prefix('auditoria')->name('auditoria.')->middleware('can:auditoria.ver')->group(function () {
        Route::get('/', [DocumentAuditLogController::class, 'index'])->name('index');
        Route::get('/documentos/{instancia}', [DocumentAuditLogController::class, 'show'])->name('show');
    });

    // ── Mantenimiento Preventivo ──
    Route::controller(MantenimientoPreventivoController::class)->name('mpreventivo.')->group(function () {
        // Dependen de un Equipo específico
        Route::prefix('equipos/{equipo}/mantenimientos')->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });
        // Rutas directas del mantenimiento
        Route::prefix('mantenimientos')->group(function () {
            Route::get('/{mantenimiento}', 'show')->name('show');
            Route::get('/{mantenimiento}/edit', 'edit')->name('edit');
            Route::put('/{mantenimiento}', 'update')->name('update');
            Route::delete('/{mantenimiento}', 'destroy')->name('destroy');
        });
    });
});

// Autenticación por defecto de Laravel/Breeze
require __DIR__ . '/auth.php';
