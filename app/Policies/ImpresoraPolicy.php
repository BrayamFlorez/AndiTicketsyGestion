<?php

namespace App\Policies;

use App\Models\Impresora;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\DB;

class ImpresoraPolicy
{
    /**
     * ¿Este usuario tiene visibilidad sobre esta impresora, según su rol?
     * - Transversales: todas.
     * - Con cobertura: las de empresas que cubre en empresa_user.
     * - Con empresa principal: solo las de su empresa.
     * - Resto: ninguna.
     */
    private function tieneAlcance(User $user, Impresora $impresora): bool
    {
        if (Roles::usuarioCumple($user, 'esTransversal')) {
            return true;
        }

        if (Roles::usuarioCumple($user, 'requiereCobertura')) {
            return DB::table('empresa_user')
                ->where('user_id', $user->id)
                ->where('empresa_id', $impresora->empresa_id)
                ->exists();
        }

        if (Roles::usuarioCumple($user, 'requiereEmpresaPrincipal')) {
            return $user->empresa_id === $impresora->empresa_id;
        }

        return false;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('impresoras.ver');
    }

    public function view(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.ver') && $this->tieneAlcance($user, $impresora);
    }

    public function create(User $user): bool
    {
        return $user->can('impresoras.crear');
    }

    public function update(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.editar') && $this->tieneAlcance($user, $impresora);
    }

    public function delete(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.eliminar') && $this->tieneAlcance($user, $impresora);
    }

    public function mantener(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.mantener') && $this->tieneAlcance($user, $impresora);
    }

    public function gestionarCatalogo(User $user): bool
    {
        return $user->can('impresoras.gestionar-catalogo');
    }

    public function gestionarConsumibles(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.mantener') && $this->tieneAlcance($user, $impresora);
    }

    public function gestionarMantenimientos(User $user, Impresora $impresora): bool
    {
        return $user->can('impresoras.mantener') && $this->tieneAlcance($user, $impresora);
    }
}
