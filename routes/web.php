<?php

use Illuminate\Support\Facades\Route;

// ── Controladores ────────────────────────────────────────────────────────
use App\Http\Controllers\AgenteWebController;
use App\Http\Controllers\ArchivoPrivadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentAuditLogController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ImpresoraConsumibleController;
use App\Http\Controllers\ImpresoraController;
use App\Http\Controllers\ImpresoraMantenimientoController;
use App\Http\Controllers\ImpresoraModeloController;
use App\Http\Controllers\MantenimientoPreventivoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyectoAdjuntoController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProyectoEtapaController;
use App\Http\Controllers\ProyectoSolicitudController;
use App\Http\Controllers\ReporteInventarioController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkflowInstanceController;
use App\Http\Controllers\WorkflowTemplateController;
use App\Http\Controllers\ImpresoraConsumibleRecargaController;
use App\Http\Controllers\ImpresoraContadorController;
use App\Http\Controllers\TicketPartesController;
/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome');

Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');

// Documentación pública
Route::view('/motor-flujos', 'publico.motor-flujos')->name('publico.motor-flujos');
Route::view('/doc-flujos', 'publico.docs-flujos')->name('publico.doc-flujos');
Route::view('/docs/impresoras', 'publico.docs-impresoras')->name('docs.impresoras');

// Rutas de prueba: solo en entorno local
if (app()->isLocal() && file_exists(__DIR__ . '/test_routes.php')) {
    require __DIR__ . '/test_routes.php';
}

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // ── Dashboard y perfil ───────────────────────────────────────────────
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // ── Notificaciones ───────────────────────────────────────────────────
    Route::controller(NotificacionController::class)->prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/leer', 'marcarLeida')->name('leer');
        Route::post('/marcar-todas-leidas', 'marcarTodasLeidas')->name('marcar-todas');
    });

    // ── Archivos privados ────────────────────────────────────────────────
    Route::get('/archivos-privados/{path}', [ArchivoPrivadoController::class, 'show'])
        ->where('path', '.*')
        ->name('archivos-privados.show');

    // ── Usuarios y roles ─────────────────────────────────────────────────
    Route::controller(UserController::class)->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', 'index')->middleware('can:usuarios.ver')->name('index');
        Route::get('/create', 'create')->middleware('can:usuarios.crear')->name('create');
        Route::post('/', 'store')->middleware('can:usuarios.crear')->name('store');
        Route::get('/{usuario}/edit', 'edit')->middleware('can:usuarios.editar')->name('edit');
        Route::put('/{usuario}', 'update')->middleware('can:usuarios.editar')->name('update');
        Route::delete('/{usuario}', 'destroy')->middleware('can:usuarios.eliminar')->name('destroy');
        Route::post('/{usuario}/toggle-activo', 'toggleActivo')->middleware('can:usuarios.editar')->name('toggle-activo');
    });

    Route::get('roles/{role}/confirmar-eliminacion', [RoleController::class, 'confirmarEliminacion'])
        ->name('roles.confirmar-eliminacion');
    Route::resource('roles', RoleController::class);

    // ── Entidades base ───────────────────────────────────────────────────
    Route::resource('empresas', EmpresaController::class)->middleware('can:empresas.gestionar');
    Route::resource('sedes', SedeController::class)->middleware('can:sedes.gestionar');

    
    // Llave para instalar el agente de inventario en los PCs de la empresa
    Route::controller(AgenteWebController::class)
        ->prefix('empresas/{empresa}/llave-agente')->name('empresas.llave-agente.')
        ->middleware('can:empresas.gestionar')
        ->group(function () {
            Route::post('/', 'generarLlave')->name('generar');
            Route::delete('/', 'revocarLlave')->name('revocar');
        });

    // ── Equipos ──────────────────────────────────────────────────────────
    Route::controller(EquipoController::class)->prefix('equipos')->name('equipos.')->group(function () {
        Route::get('/', 'index')->middleware('can:equipos.ver')->name('index');
        Route::get('/create', 'create')->middleware('can:equipos.crear')->name('create');
        Route::post('/', 'store')->middleware('can:equipos.crear')->name('store');
        Route::get('/{equipo}', 'show')->middleware('can:equipos.ver')->name('show');
        Route::get('/{equipo}/edit', 'edit')->middleware('can:equipos.editar')->name('edit');
        Route::put('/{equipo}', 'update')->middleware('can:equipos.editar')->name('update');
        Route::patch('/{equipo}/estado', 'cambiarEstado')->middleware('can:equipos.editar')->name('cambiar-estado');
        Route::delete('/{equipo}', 'destroy')->middleware('can:equipos.eliminar')->name('destroy');
        Route::delete('equipos/{equipo}/agente', [AgenteWebController::class, 'revocarAgente'])
         ->middleware('can:equipos.editar')->name('equipos.agente.revocar');
    });

    // ── Mantenimiento preventivo de equipos ──────────────────────────────
    Route::controller(MantenimientoPreventivoController::class)->name('mpreventivo.')->group(function () {
        // Dependen de un equipo
        Route::get('equipos/{equipo}/mantenimientos/create', 'create')->name('create');
        Route::post('equipos/{equipo}/mantenimientos', 'store')->name('store');

        // Sobre el mantenimiento directamente
        Route::get('mantenimientos/{mantenimiento}', 'show')->name('show');
        Route::get('mantenimientos/{mantenimiento}/edit', 'edit')->name('edit');
        Route::put('mantenimientos/{mantenimiento}', 'update')->name('update');
        Route::delete('mantenimientos/{mantenimiento}', 'destroy')->name('destroy');
    });

    // ── Reportes ─────────────────────────────────────────────────────────
    Route::controller(ReporteInventarioController::class)->prefix('reportes/inventario')->name('reportes.inventario.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/excel', 'excel')->name('excel');
        Route::get('/pdf', 'pdf')->name('pdf');
    });

    // ── Tickets ──────────────────────────────────────────────────────────
    Route::controller(TicketController::class)->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->middleware('can:tickets.crear')->name('create');
        Route::post('/', 'store')->middleware('can:tickets.crear')->name('store');
        Route::get('/{ticket}', 'show')->name('show');
        Route::post('/{ticket}/asignar', 'asignar')->middleware('can:tickets.asignar')->name('asignar');
        Route::post('/{ticket}/iniciar', 'iniciar')->middleware('can:tickets.iniciar')->name('iniciar');
        Route::post('/{ticket}/resolver', 'resolver')->middleware('can:tickets.resolver')->name('resolver');
        Route::post('/{ticket}/cerrar', 'cerrar')->middleware('can:tickets.cerrar')->name('cerrar');
        Route::post('/{ticket}/cancelar', 'cancelar')->middleware('can:tickets.cancelar')->name('cancelar');
        Route::post('/{ticket}/partes', [TicketPartesController::class, 'solicitar'])->middleware('can:tickets.resolver')->name('partes.solicitar');
        Route::post('/{ticket}/partes/recibir', [TicketPartesController::class, 'recibir'])->middleware('can:tickets.resolver')->name('partes.recibir');
        Route::delete('/{ticket}', 'destroy')->middleware('can:tickets.eliminar')->name('destroy');
    });

    // ── Impresoras ───────────────────────────────────────────────────────
    // El catálogo va ANTES del resource: si no, "impresoras/catalogo" lo captura impresoras.show
    Route::controller(ImpresoraModeloController::class)->prefix('impresoras/catalogo')->name('impresoras.catalogo.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/nuevo', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{modelo}', 'edit')->name('edit');
        Route::put('/{modelo}', 'update')->name('update');
        Route::patch('/{modelo}/toggle', 'toggleActivo')->name('toggle');
        Route::delete('/{modelo}', 'destroy')->name('destroy');
    });

    Route::resource('impresoras', ImpresoraController::class)
        ->parameters(['impresoras' => 'impresora']);

    Route::prefix('impresoras/{impresora}')->name('impresoras.')->group(function () {
        // Consumibles
        Route::controller(ImpresoraConsumibleController::class)->prefix('consumibles')->name('consumibles.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{consumible}', 'update')->name('update');
            Route::post('/{consumible}/reemplazar', 'reemplazar')->name('reemplazar');
            Route::delete('/{consumible}', 'destroy')->name('destroy');
        });

        // Recarga parcial (solo en tiempo real) y lecturas del contador
        Route::post('consumibles/{consumible}/recargar', [ImpresoraConsumibleRecargaController::class, 'store'])->name('consumibles.recargar');
        Route::post('contadores', [ImpresoraContadorController::class, 'store'])->name('contadores.store');

        // Mantenimientos
        Route::controller(ImpresoraMantenimientoController::class)->prefix('mantenimientos')->name('mantenimientos.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::delete('/{mantenimiento}', 'destroy')->name('destroy');
        });
    });

    // ── Clientes y proyectos ─────────────────────────────────────────────
    Route::resource('clientes', ClienteController::class)
        ->except(['show'])
        ->middleware('can:clientes.gestionar');

    // index, create, store, show, edit, update, destroy (el acceso lo valida ProyectoPolicy)
    Route::resource('proyectos', ProyectoController::class);

    Route::prefix('proyectos/{proyecto}')->name('proyectos.')->group(function () {
        // Etapas
        Route::controller(ProyectoEtapaController::class)->prefix('etapas')->name('etapas.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{etapa}', 'update')->name('update');
            Route::delete('/{etapa}', 'destroy')->name('destroy');
        });

        // Solicitudes del cliente
        Route::controller(ProyectoSolicitudController::class)->prefix('solicitudes')->name('solicitudes.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{solicitud}', 'update')->name('update');
            Route::patch('/{solicitud}/estado', 'estado')->name('estado');
            Route::delete('/{solicitud}', 'destroy')->name('destroy');
        });

        // Evidencias (disco privado)
        Route::controller(ProyectoAdjuntoController::class)->prefix('adjuntos')->name('adjuntos.')->group(function () {
            Route::get('/{adjunto}', 'show')->name('show');
            Route::delete('/{adjunto}', 'destroy')->name('destroy');
        });
    });

    // ── Flujos documentales ──────────────────────────────────────────────
    Route::prefix('flujos')->name('flujos.')->group(function () {

        // Tipos de documento
        Route::controller(DocumentTypeController::class)
            ->prefix('tipos-documento')->name('tipos-documento.')
            ->middleware('can:flujos.tipos-documento.gestionar')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{tipoDocumento}/edit', 'edit')->name('edit');
                Route::put('/{tipoDocumento}', 'update')->name('update');
                Route::delete('/{tipoDocumento}', 'destroy')->name('destroy');
            });

        // Plantillas
        Route::controller(WorkflowTemplateController::class)
            ->prefix('plantillas')->name('plantillas.')
            ->middleware('can:flujos.plantillas.gestionar')
            ->group(function () {
                // Antes de /{plantilla} para que "api" no se tome como plantilla
                Route::get('/api/usuarios', 'buscarUsuarios')->name('api.usuarios');

                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{plantilla}/edit', 'edit')->name('edit');
                Route::put('/{plantilla}', 'update')->name('update');
                Route::delete('/{plantilla}', 'destroy')->name('destroy');
                Route::post('/{plantilla}/toggle-activo', 'toggleActivo')->name('toggle-activo');
            });

        // Instancias
        Route::controller(WorkflowInstanceController::class)
            ->prefix('instancias')->name('instancias.')
            ->group(function () {
                Route::get('/', 'index')->middleware('can:flujos.ver-participando')->name('index');
                Route::get('/create', 'create')->middleware('can:flujos.iniciar')->name('create');
                Route::post('/', 'store')->middleware('can:flujos.iniciar')->name('store');
                Route::get('/{instancia}', 'show')->name('show');
                Route::patch('/{instancia}/cabecera', 'actualizarCabecera')->name('cabecera');

                // Pasos
                Route::post('/{instancia}/steps/{step}/comentarios', 'agregarComentarioStep')->name('steps.comentar');
                Route::patch('/{instancia}/steps/{step}/codigo-referencia', 'actualizarCodigoReferenciaStep')->name('steps.codigo-referencia');

                // Adjuntos
                Route::post('/{instancia}/adjuntos', 'subirAdjunto')->name('adjuntos.subir');
                Route::get('/{instancia}/adjuntos/{attachment}/descargar', 'descargarAdjunto')->name('adjuntos.descargar');
                Route::delete('/{instancia}/adjuntos/{attachment}', 'eliminarAdjunto')->name('adjuntos.eliminar');

                // Asignaciones y acciones
                Route::post('/{instancia}/asignaciones/{assignment}/actuar', 'actuar')->name('asignaciones.actuar');
                Route::post('/{instancia}/corregir', 'corregir')->name('corregir');
                Route::post('/{instancia}/desistir', 'desistir')->name('desistir');
                Route::post('/{instancia}/cancelar', 'cancelar')->middleware('can:flujos.cancelar-cualquiera')->name('cancelar');
                Route::post('/{instancia}/reabrir', 'reabrir')->middleware('can:flujos.reabrir')->name('reabrir');
            });
    });

    // ── Auditoría documental (solo lectura) ──────────────────────────────
    Route::controller(DocumentAuditLogController::class)
        ->prefix('auditoria')->name('auditoria.')
        ->middleware('can:auditoria.ver')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/documentos/{instancia}', 'show')->name('show');
        });

    // ── Configuración global (solo super-admin) ──────────────────────────
    Route::controller(SettingsController::class)
        ->prefix('configuracion')->name('configuracion.')
        ->middleware('role:super-admin')
        ->group(function () {
            Route::get('/ajustes', 'index')->name('ajustes.index');
            Route::patch('/ajustes/{setting}', 'update')->name('ajustes.update');
        });
});

// Autenticación (Breeze)
require __DIR__ . '/auth.php';
