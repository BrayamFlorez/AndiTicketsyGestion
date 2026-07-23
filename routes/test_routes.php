<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStepAssignment;
use App\Models\WorkflowTemplate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Actions\Flujos\ActuarEnAsignacionAction;
use App\Actions\Flujos\CancelarFlujoAction;
use App\Actions\Flujos\EnviarCorreccionAction;
use App\Actions\Flujos\IniciarFlujoAction;
use App\Actions\Flujos\ReabrirFlujoAction;

// ==========================================
// RUTA 1: PRUEBA DEL FLUJO COMPLETO
// ==========================================
Route::get('/test-tinker', function () {
    try {
        // ── 1. Iniciar un flujo ──────────────────────────────────────────────
        $iniciador = User::first();
        $plantilla = WorkflowTemplate::activos()->firstOrFail();

        $instance = app(IniciarFlujoAction::class)->ejecutar($plantilla, $iniciador, [
            'titulo' => 'Cotización cliente ACME',
            'descripcion' => 'Cotización de servicios recurrentes 2026.',
        ]);

        dump("Código: {$instance->codigo}");
        dump("Etapa actual: {$instance->etapaActual->nombre}");

        // ── 2. Ver asignaciones pendientes de esa etapa ──────────────────────
        $asignaciones = $instance->steps()->latest()->first()->assignments;
        $asignaciones->each(fn ($a) => dump(
            "Asig #{$a->id} | user_id={$a->user_id} | stage_actor={$a->workflow_stage_actor_id} | {$a->estado}"
        ));

        // ── 3. Aprobar una (simular un actor autorizado) ─────────────────────
        $asignacion = $asignaciones->first();
        $actor = $asignacion->user_id
            ? User::find($asignacion->user_id)
            : User::role(Role::find($asignacion->stageActor->actor_id)->name)->first();

        // Verificamos si el actor existe para evitar errores silenciosos
        dump("El actor encontrado es:");
        dump($actor);

        app(ActuarEnAsignacionAction::class)->ejecutar($asignacion, $actor, 'aprobar', 'OK, todo en orden.');

        $instance->refresh();
        dump("Estado tras aprobar: {$instance->estado}");
        dump("Etapa actual: " . ($instance->etapaActual?->nombre ?? 'ninguna (cerrado)'));

        // ── 4. Rechazar en la etapa siguiente para probar la política ────────
        $stepActual = $instance->steps()->where('estado', 'en_curso')->first();
        $asig = $stepActual->assignments->first();
        $actor2 = $asig->user_id
            ? User::find($asig->user_id)
            : User::role(Role::find($asig->stageActor->actor_id)->name)->first();

        app(ActuarEnAsignacionAction::class)->ejecutar($asig, $actor2, 'rechazar', 'Falta información de precios.');

        $instance->refresh();
        dump("Estado tras rechazar: {$instance->estado}");

        // ── 5. Si quedó en requiere_correccion, reenviar ─────────────────────
        if ($instance->estado === WorkflowInstance::ESTADO_REQUIERE_CORRECCION) {
            app(EnviarCorreccionAction::class)->ejecutar($instance, $iniciador);
            $instance->refresh();
            dump("Tras corrección → estado={$instance->estado}, revisión={$instance->revision}");
        }

        // ── 6. Cancelar y reabrir ────────────────────────────────────────────
        app(CancelarFlujoAction::class)->ejecutar($instance, $iniciador, 'Prueba manual.');
        dump("Cancelado: estado={$instance->fresh()->estado}");

        app(ReabrirFlujoAction::class)->ejecutar($instance->fresh());
        dump("Reabierto: estado={$instance->fresh()->estado}, etapa={$instance->fresh()->etapaActual->nombre}");

        return "<br><br><strong>✅ Prueba finalizada con éxito.</strong>";

    } catch (\Throwable $e) {
        dump("💥 ¡Ups! El código se detuvo por un error:");
        dump("Mensaje: " . $e->getMessage());
        dump("Archivo: " . $e->getFile() . " en la línea " . $e->getLine());
        return "<br><br><strong>⚠️ Prueba interrumpida.</strong>";
    }
});


// ==========================================
// RUTA 2: PRUEBA DE ROLES Y USUARIOS
// ==========================================
Route::get('/test-roles', function () {
    try {
        dump("── Roles existentes ──");
        Role::all(['id','name','guard_name'])->each(fn ($r) =>
            dump("  #{$r->id} · {$r->name} · guard={$r->guard_name}")
        );

        dump("── Permisos de flujos ──");
        Permission::where('name', 'like', 'flujos.%')
            ->get(['id','name'])
            ->each(fn ($p) => dump("  #{$p->id} · {$p->name}"));

        dump("── Usuarios y sus roles ──");
        User::with('roles:id,name')->get(['id','name','email'])->each(function ($u) {
            $roles = $u->roles->pluck('name')->join(', ') ?: '(sin rol)';
            dump("  #{$u->id} · {$u->name} <{$u->email}> → [{$roles}]");
        });

        return "<br><br><strong>✅ Consulta de roles y usuarios finalizada con éxito.</strong>";

    } catch (\Throwable $e) {
        dump("💥 ¡Ups! Hubo un error:");
        dump("Mensaje: " . $e->getMessage());
        dump("Archivo: " . $e->getFile() . " en la línea " . $e->getLine());
        return "<br><br><strong>⚠️ Prueba interrumpida.</strong>";
    }
});