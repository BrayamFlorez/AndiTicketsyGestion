<?php

namespace App\Support;

/**
 * Clasificación de roles del sistema según CÓMO se vinculan con empresas y sedes.
 *
 * Cada rol pertenece a UN solo grupo. Los tres grupos son mutuamente excluyentes
 * y determinan qué sección muestra el formulario de usuarios, y qué empresas
 * puede elegir la persona al crear un ticket.
 *
 * ┌─────────────────────────┬──────────────────────────────┬─────────────────────────┐
 * │ Grupo                   │ Dónde vive el vínculo        │ Alcance                 │
 * ├─────────────────────────┼──────────────────────────────┼─────────────────────────┤
 * │ CON_COBERTURA           │ tabla empresa_user (N filas) │ Las empresas/sedes que  │
 * │                         │                              │ tiene asignadas         │
 * ├─────────────────────────┼──────────────────────────────┼─────────────────────────┤
 * │ CON_EMPRESA_PRINCIPAL   │ users.empresa_id / sede_id   │ Solo SU empresa/sede    │
 * ├─────────────────────────┼──────────────────────────────┼─────────────────────────┤
 * │ TRANSVERSALES           │ (ninguno)                    │ Todas las empresas      │
 * └─────────────────────────┴──────────────────────────────┴─────────────────────────┘
 */
class Roles
{
    /**
     * ── GRUPO 1: Con cobertura de soporte ──────────────────────────────
     *
     * Atienden tickets de VARIAS empresas/sedes. El vínculo se guarda como
     * filas en la tabla `empresa_user`:
     *   (user_id, empresa_id, sede_id = NULL)  → cubre TODA la empresa
     *   (user_id, empresa_id, sede_id = 7)     → cubre solo esa sede
     *
     * En el formulario de usuarios ven la sección "Coberturas asignadas".
     * Al crear un ticket, eligen empresa entre las que tienen cubiertas.
     * Pueden recibir asignación de tickets.
     */
    public const CON_COBERTURA = [
        'tecnico',
        'lider-tecnico',
    ];

    /**
     * ── GRUPO 2: Vinculados a una empresa ──────────────────────────────
     *
     * Trabajan EN un lugar concreto. El vínculo son las columnas
     * `users.empresa_id` y `users.sede_id`.
     *
     * En el formulario de usuarios ven la sección "Vinculación a empresa".
     * Al crear un ticket, este hereda automáticamente su empresa/sede —
     * no eligen destino.
     */
    public const CON_EMPRESA_PRINCIPAL = [
        'usuario',
        'asesor-ventas',
        'auxiliar-bodega',
        'auxiliar-administrativo',
        'auxiliar-cartera',
        'auxiliar-contabilidad',
        'recepcion',
        'talento-humano',
    ];

    /**
     * ── GRUPO 3: Transversales (sin restricción) ───────────────────────
     *
     * Atraviesan toda la organización: ven y operan sobre TODAS las
     * empresas del sistema. No tienen vínculo guardado en ningún lado —
     * su alcance sale del rol mismo.
     *
     * En el formulario de usuarios solo ven un aviso informativo.
     * Al crear un ticket, eligen cualquier empresa activa.
     */
    public const TRANSVERSALES = [
        'super-admin',
        'admin',
        'gerente',
        'lider-ventas',
    ];

    // ─────────────────────────────────────────────────────────────────────
    //  Consultas por rol individual
    // ─────────────────────────────────────────────────────────────────────

    /**
     * ¿Este rol necesita filas en empresa_user?
     * Usado por: formulario de usuarios (mostrar sección de coberturas),
     * UserController (sincronizar o limpiar coberturas al guardar).
     */
    public static function requiereCobertura(?string $rol): bool
    {
        return in_array($rol, self::CON_COBERTURA, true);
    }

    /**
     * ¿Este rol se vincula a una sola empresa/sede?
     * Usado por: formulario de usuarios (mostrar selects de empresa/sede),
     * UserController (guardar o anular users.empresa_id / sede_id).
     */
    public static function requiereEmpresaPrincipal(?string $rol): bool
    {
        return in_array($rol, self::CON_EMPRESA_PRINCIPAL, true);
    }

    /**
     * ¿Este rol tiene acceso a todas las empresas sin restricción?
     * Usado por: formulario de usuarios (aviso informativo),
     * TicketController (ofrecer todas las empresas al crear ticket).
     */
    public static function esTransversal(?string $rol): bool
    {
        return in_array($rol, self::TRANSVERSALES, true);
    }

    /**
     * ¿Este rol ELIGE la empresa/sede destino al crear un ticket,
     * en vez de heredar la suya automáticamente?
     *
     * Verdadero para transversales (eligen entre todas) y para roles con
     * cobertura (eligen entre las que cubren).
     */
    public static function eligeDestinoTicket(?string $rol): bool
    {
        return self::esTransversal($rol) || self::requiereCobertura($rol);
    }

    /**
     * ¿Este rol puede ser asignado como responsable de un ticket?
     * Hoy coincide con "tiene cobertura", pero se separa por claridad:
     * si mañana un gerente debe poder recibir tickets, se cambia solo acá.
     */
    public static function puedeRecibirTickets(?string $rol): bool
    {
        return self::requiereCobertura($rol);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Consultas sobre un usuario (que puede tener varios roles)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Verifica si ALGUNO de los roles del usuario cumple el criterio dado.
     *
     * Ejemplo:
     *   Roles::usuarioCumple($user, 'esTransversal')  → bool
     *
     * Evita repetir el `$user->roles->contains(fn($r) => ...)` en cada controller.
     */
    public static function usuarioCumple(\App\Models\User $user, string $criterio): bool
    {
        if (! method_exists(self::class, $criterio)) {
            return false;
        }

        return $user->roles->contains(
            fn ($rol) => self::{$criterio}($rol->name)
        );
    }

    /**
     * Todos los roles conocidos por el sistema, agrupados.
     * Útil para depurar o para poblar selects agrupados en formularios.
     */
    public static function todosAgrupados(): array
    {
        return [
            'Transversales'         => self::TRANSVERSALES,
            'Con cobertura'         => self::CON_COBERTURA,
            'Vinculados a empresa'  => self::CON_EMPRESA_PRINCIPAL,
        ];
    }

    /**
     * Roles registrados en esta clase. Sirve para detectar roles en la BD
     * que quedaron sin clasificar (caerían en ningún grupo y romperían la UI).
     */
    public static function todos(): array
    {
        return array_merge(
            self::TRANSVERSALES,
            self::CON_COBERTURA,
            self::CON_EMPRESA_PRINCIPAL,
        );
    }
}