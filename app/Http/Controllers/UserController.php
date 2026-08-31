<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('usuarios.ver'), 403);

        $query = User::query()
            ->with(['roles:id,name', 'empresa:id,nombre', 'sede:id,nombre']);

        $this->aplicarFiltros($query, $request);

        $usuarios = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Conteo de coberturas para roles que las usan (1 query, evita N+1)
        $idsConCobertura = $usuarios
            ->filter(fn ($u) => $u->roles->contains(fn ($r) => Roles::requiereCobertura($r->name)))
            ->pluck('id');

        $coberturasPorTecnico = DB::table('empresa_user')
            ->whereIn('user_id', $idsConCobertura)
            ->select('user_id', DB::raw('COUNT(DISTINCT empresa_id) as total_empresas'))
            ->groupBy('user_id')
            ->pluck('total_empresas', 'user_id');

        return view('usuarios.index', [
            'usuarios'             => $usuarios,
            'filtros'              => $this->filtrosActivos($request),
            'roles'                => $this->rolesDisponiblesPara(Auth::user()),
            'empresas'             => Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'coberturasPorTecnico' => $coberturasPorTecnico,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        abort_unless(Auth::user()->can('usuarios.crear'), 403);

        return view('usuarios.create', $this->datosParaFormulario());
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $usuario = DB::transaction(function () use ($request) {

            $rol = $request->input('rol');

            // Empresa/sede principal solo para roles vinculados a un lugar de trabajo
            $vinculado  = Roles::requiereEmpresaPrincipal($rol);
            $empresa_id = $vinculado ? $request->input('empresa_id') : null;
            $sede_id    = $vinculado ? $request->input('sede_id') : null;

            $usuario = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'empresa_id' => $empresa_id,
                'sede_id'    => $sede_id,
                'activo'     => $request->boolean('activo', true),
            ]);

            $usuario->assignRole($rol);

            // Coberturas para roles que operan sobre empresas/sedes asignadas
            if (Roles::requiereCobertura($rol)) {
                $this->sincronizarCoberturas($usuario, $request->input('coberturas', []));
            }

            return $usuario;
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} creado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(User $usuario): View
    {
        abort_unless(Auth::user()->can('usuarios.editar'), 403);
        $this->bloquearSiNoPuedeGestionar($usuario);

        $usuario->load(['roles', 'empresasAsignadas']);

        // Coberturas actuales para precargar el formulario
        $coberturasActuales = DB::table('empresa_user')
            ->where('user_id', $usuario->id)
            ->orderBy('empresa_id')
            ->get(['empresa_id', 'sede_id'])
            ->map(fn ($fila) => [
                'empresa_id' => (int) $fila->empresa_id,
                'sede_id'    => $fila->sede_id !== null ? (int) $fila->sede_id : '',
            ])
            ->values()
            ->all();

        return view('usuarios.edit', array_merge(
            $this->datosParaFormulario(),
            [
                'usuario'            => $usuario,
                'rolActual'          => $usuario->roles->first()?->name ?? '',
                'coberturasActuales' => $coberturasActuales,
            ]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $this->bloquearSiNoPuedeGestionar($usuario);

        DB::transaction(function () use ($request, $usuario) {

            $rol = $request->input('rol');

            $vinculado  = Roles::requiereEmpresaPrincipal($rol);
            $empresa_id = $vinculado ? $request->input('empresa_id') : null;
            $sede_id    = $vinculado ? $request->input('sede_id') : null;

            $data = [
                'name'       => $request->name,
                'email'      => $request->email,
                'empresa_id' => $empresa_id,
                'sede_id'    => $sede_id,
                'activo'     => $request->boolean('activo'),
            ];

            // Password solo se actualiza si se ingresó algo
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $usuario->update($data);
            $usuario->syncRoles([$rol]);

            // Coberturas: sincronizar si el rol las usa, limpiar si no
            if (Roles::requiereCobertura($rol)) {
                $this->sincronizarCoberturas($usuario, $request->input('coberturas', []));
            } else {
                DB::table('empresa_user')->where('user_id', $usuario->id)->delete();
            }
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} actualizado correctamente.");
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        abort_unless($request->user()->can('usuarios.eliminar'), 403);

        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        if ($usuario->hasRole('super-admin') && ! $request->user()->hasRole('super-admin')) {
            return back()->with('error', 'No puedes eliminar a un super administrador.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$nombre} eliminado.");
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE ACTIVO — acción rápida desde el listado
    |--------------------------------------------------------------------------
    */
    public function toggleActivo(Request $request, User $usuario): RedirectResponse
    {
        abort_unless($request->user()->can('usuarios.editar'), 403);
        $this->bloquearSiNoPuedeGestionar($usuario);

        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$usuario->name} {$estado}.");
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Roles que el creador/editor puede asignar.
     * Evita escalamiento: nadie otorga un rol de mayor autoridad que el suyo.
     */
    private function rolesDisponiblesPara(User $user): array
    {
        $todos = Role::orderBy('name')->pluck('name')->toArray();

        if ($user->hasRole('super-admin')) {
            return $todos;
        }

        $restringidos = ['super-admin', 'admin'];

        // Solo admin (o super-admin) puede otorgar el rol gerente
        if (! $user->hasRole('admin')) {
            $restringidos[] = 'gerente';
        }

        return array_values(array_diff($todos, $restringidos));
    }

    /**
     * Datos comunes para create/edit.
     * Incluye los grupos de roles para que el formulario decida qué sección mostrar.
     */
    private function datosParaFormulario(): array
    {
        return [
            'roles'           => $this->rolesDisponiblesPara(Auth::user()),
            'empresas'        => Empresa::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'sedesPorEmpresa' => Sede::activas()
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'empresa_id'])
                ->groupBy('empresa_id'),

            'rolesConCobertura'  => Roles::CON_COBERTURA,
            'rolesConEmpresa'    => Roles::CON_EMPRESA_PRINCIPAL,
            'rolesTransversales' => Roles::TRANSVERSALES,
        ];
    }

    /**
     * Bloquea acciones de admin sobre super-admins.
     */
    private function bloquearSiNoPuedeGestionar(User $objetivo): void
    {
        $actor = Auth::user();
        if ($objetivo->hasRole('super-admin') && ! $actor->hasRole('super-admin')) {
            abort(403, 'No tienes permiso para gestionar a un super administrador.');
        }
    }

    /**
     * Sincroniza las filas de empresa_user para un usuario con cobertura.
     * Reemplaza todas las coberturas actuales por las nuevas.
     * Descarta pares inválidos (sede que no pertenece a la empresa indicada).
     */
    private function sincronizarCoberturas(User $usuario, array $coberturas): void
    {
        $entradas = collect($coberturas)
            ->filter(fn ($c) => ! empty($c['empresa_id']))
            ->map(fn ($c) => [
                'empresa_id' => (int) $c['empresa_id'],
                'sede_id'    => ! empty($c['sede_id']) ? (int) $c['sede_id'] : null,
            ])
            // Deduplicar: misma empresa+sede no se repite
            ->unique(fn ($c) => $c['empresa_id'] . '|' . ($c['sede_id'] ?? 'NULL'))
            ->values();

        // Mapa sede_id => empresa_id para validar coherencia
        $sedeIds = $entradas->pluck('sede_id')->filter()->unique();

        $sedesValidas = $sedeIds->isEmpty()
            ? collect()
            : Sede::whereIn('id', $sedeIds)->pluck('empresa_id', 'id');

        $filas = $entradas
            ->filter(function ($c) use ($sedesValidas) {
                // Cobertura global de empresa: siempre válida
                if ($c['sede_id'] === null) {
                    return true;
                }
                // Cobertura de sede: la sede debe pertenecer a esa empresa
                return ($sedesValidas[$c['sede_id']] ?? null) === $c['empresa_id'];
            })
            ->map(fn ($c) => [
                'user_id'     => $usuario->id,
                'empresa_id'  => $c['empresa_id'],
                'sede_id'     => $c['sede_id'],
                'asignado_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ])
            ->values()
            ->all();

        DB::table('empresa_user')->where('user_id', $usuario->id)->delete();

        if (! empty($filas)) {
            DB::table('empresa_user')->insert($filas);
        }
    }

    private function aplicarFiltros(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('rol'),        fn ($q) => $q->role($request->rol))
            ->when($request->filled('empresa_id'), fn ($q) => $q->where('empresa_id', $request->empresa_id))
            ->when($request->filled('activo'),     fn ($q) => $q->where('activo', $request->activo === '1'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . trim($request->q) . '%';
                $q->where(fn ($sub) => $sub
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term));
            });
    }

    private function filtrosActivos(Request $request): array
    {
        return collect($request->only(['rol', 'empresa_id', 'activo', 'q']))
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->toArray();
    }
}