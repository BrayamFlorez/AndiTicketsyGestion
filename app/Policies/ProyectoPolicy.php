<?php

namespace App\Policies;

use App\Models\Proyecto;
use App\Models\ProyectoAdjunto;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Reglas de acceso a proyectos:
 * - Ver:        'proyectos.ver-todos', o 'proyectos.ver' siendo miembro del equipo.
 * - Editar:     'proyectos.editar' + acceso al proyecto, o ser el responsable.
 * - Registrar:  'proyectos.registrar' + acceso al proyecto (etapas y solicitudes).
 * - Modificar un registro ajeno: solo el autor, el responsable o quien tenga 'proyectos.editar'.
 */
class ProyectoPolicy
{
    private function tieneAcceso(User $user, Proyecto $proyecto): bool
    {
        return $user->can('proyectos.ver-todos')
            || ($user->can('proyectos.ver') && $proyecto->esMiembro($user));
    }

    public function viewAny(User $user): bool
    {
        return $user->can('proyectos.ver') || $user->can('proyectos.ver-todos');
    }

    public function view(User $user, Proyecto $proyecto): bool
    {
        return $this->tieneAcceso($user, $proyecto);
    }

    public function create(User $user): bool
    {
        return $user->can('proyectos.crear');
    }

    public function update(User $user, Proyecto $proyecto): bool
    {
        if (! $this->tieneAcceso($user, $proyecto)) {
            return false;
        }

        return $user->can('proyectos.editar') || $proyecto->responsable_id === $user->id;
    }

    public function delete(User $user, Proyecto $proyecto): bool
    {
        return $user->can('proyectos.eliminar') && $this->tieneAcceso($user, $proyecto);
    }

    /** Registrar etapas y solicitudes */
    public function registrar(User $user, Proyecto $proyecto): bool
    {
        return $user->can('proyectos.registrar') && $this->tieneAcceso($user, $proyecto);
    }

    /**
     * Editar/eliminar una etapa, solicitud o adjunto concreto.
     * Nadie modifica lo que registró otro, salvo el responsable o un editor.
     */
    public function modificarRegistro(User $user, Proyecto $proyecto, Model $registro): bool
    {
        if (! $this->registrar($user, $proyecto)) {
            return false;
        }

        $autorId = $registro instanceof ProyectoAdjunto
            ? $registro->subido_por
            : $registro->registrado_por;

        return $autorId === $user->id
            || $proyecto->responsable_id === $user->id
            || $user->can('proyectos.editar');
    }
}
