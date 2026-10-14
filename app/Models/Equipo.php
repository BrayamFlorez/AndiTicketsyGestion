<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';

    const CATEGORIAS = [
        'computadora' => 'Computadora de escritorio',
        'portatil'    => 'Portátil / Laptop',
        'aio'         => 'Todo en uno (AIO)',
        'impresora'   => 'Impresora',
    ];

    // "Tipo de equipo" del reporte de servicio
    const TIPOS_EQUIPO = [
        'computadora' => 'Cómputo',
        'portatil'    => 'Cómputo',
        'aio'         => 'Cómputo',
        'impresora'   => 'Impresión',
    ];

    // Categorías a las que se les registra mantenimiento en este módulo (las impresoras tienen el suyo)
    const CATEGORIAS_CON_MANTENIMIENTO = ['computadora', 'portatil', 'aio'];

    const ESTADOS = [
        'activo'         => 'Activo',
        'mantenimiento'  => 'En mantenimiento',
        'dado_de_baja'   => 'Dado de baja',
    ];

    const COLORES_ESTADO = [
        'activo'         => 'resuelto',
        'mantenimiento'  => 'en_progreso',
        'dado_de_baja'   => 'cerrado',
    ];

    const ESPECIFICACIONES_SCHEMA = [

        'computadora' => [
            'procesador'        => ['label' => 'Procesador',         'type' => 'text',   'placeholder' => 'Ej: Intel Core i5-12400'],
            'ram_gb'            => ['label' => 'RAM (GB)',           'type' => 'number'],
            'almacenamiento_gb' => ['label' => 'Almacenamiento (GB)','type' => 'number'],
            'tipo_disco'        => ['label' => 'Tipo de disco',      'type' => 'select', 'options' => ['HDD', 'SSD', 'NVMe']],
            'sistema_operativo' => ['label' => 'Sistema operativo',  'type' => 'text',   'placeholder' => 'Ej: Windows 11 Pro'],
            'tarjeta_grafica'   => ['label' => 'Tarjeta gráfica',    'type' => 'text',   'required' => false, 'placeholder' => 'Opcional'],
        ],

        'aio' => [
            'procesador'        => ['label' => 'Procesador',          'type' => 'text',   'placeholder' => 'Ej: Intel Core i5-12400T'],
            'ram_gb'            => ['label' => 'RAM (GB)',            'type' => 'number'],
            'almacenamiento_gb' => ['label' => 'Almacenamiento (GB)', 'type' => 'number'],
            'tipo_disco'        => ['label' => 'Tipo de disco',       'type' => 'select', 'options' => ['HDD', 'SSD', 'NVMe']],
            'sistema_operativo' => ['label' => 'Sistema operativo',   'type' => 'text',   'placeholder' => 'Ej: Windows 11 Pro'],
            'pantalla_pulgadas' => ['label' => 'Tamaño de pantalla (pulgadas)', 'type' => 'number'],
        ],

        'portatil' => [
            'procesador'        => ['label' => 'Procesador',          'type' => 'text', 'placeholder' => 'Ej: AMD Ryzen 5 5500U'],
            'ram_gb'            => ['label' => 'RAM (GB)',            'type' => 'number'],
            'almacenamiento_gb' => ['label' => 'Almacenamiento (GB)', 'type' => 'number'],
            'tipo_disco'        => ['label' => 'Tipo de disco',       'type' => 'select', 'options' => ['HDD', 'SSD', 'NVMe']],
            'sistema_operativo' => ['label' => 'Sistema operativo',   'type' => 'text'],
            'pantalla_pulgadas' => ['label' => 'Tamaño de pantalla (pulgadas)', 'type' => 'number'],
            'estado_bateria'    => ['label' => 'Estado de la batería', 'type' => 'select', 'options' => ['Excelente', 'Buena', 'Regular', 'Deficiente'], 'required' => false],
        ],

        'impresora' => [
            'tipo'                     => ['label' => 'Tipo de impresora', 'type' => 'select', 'options' => ['Láser', 'Inyección de tinta', 'Matricial', 'Multifuncional']],
            'color'                    => ['label' => 'Color',             'type' => 'select', 'options' => ['Color', 'Blanco y Negro']],
            'conectividad'             => ['label' => 'Conectividad',      'type' => 'select', 'options' => ['USB', 'Red Ethernet', 'Wi-Fi', 'USB + Red', 'USB + Wi-Fi', 'Red + Wi-Fi']],
            'velocidad_ppm'            => ['label' => 'Velocidad (páginas por minuto)', 'type' => 'number', 'required' => false],
            'contador_paginas_inicial' => ['label' => 'Contador inicial de páginas',    'type' => 'number', 'required' => false],
        ],
    ];

    protected $fillable = [
        'empresa_id', 'sede_id', 'responsable_id', 'creado_por',
        'categoria', 'nombre', 'marca', 'modelo', 'numero_serie', 'codigo_interno',
        'ubicacion', 'estado', 'especificaciones', 'notas',
    ];

    protected $casts = [
        'especificaciones' => 'array',
    ];

    // Relaciones

    public function empresa(): BelongsTo      { return $this->belongsTo(Empresa::class); }
    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class); }
    public function responsable(): BelongsTo  { return $this->belongsTo(User::class, 'responsable_id'); }
    public function creador(): BelongsTo      { return $this->belongsTo(User::class, 'creado_por'); }

    public function perifericos(): HasMany
    {
        return $this->hasMany(EquipoPeriferico::class)->orderBy('tipo')->orderBy('id');
    }

    /** Servicios técnicos (preventivos y correctivos), del más reciente al más antiguo */
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(EquipoMantenimiento::class)->latest('fecha_servicio')->latest('id');
    }
    
    // Cuando agreguemos el módulo de servicios:
    // public function servicios(): HasMany {
    //     return $this->hasMany(Servicio::class);
    // }

    // Scopes

    public function scopeActivos($query)        { return $query->where('estado', 'activo'); }
    public function scopeDeEmpresa($query, int $empresaId)    { return $query->where('empresa_id', $empresaId); }
    public function scopeDeCategoria($query, string $cat)     { return $query->where('categoria', $cat); }

    public function agente(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EquipoAgente::class);
    }

    public function scopeAccesiblesParaTecnico($query, User $tecnico)
    {
        return $query->whereExists(function ($sub) use ($tecnico) {
            $sub->from('empresa_user')
                ->where('empresa_user.user_id', $tecnico->id)
                ->whereColumn('empresa_user.empresa_id', 'equipos.empresa_id')
                ->where(function ($w) {
                    $w->whereNull('empresa_user.sede_id')
                      ->orWhereColumn('empresa_user.sede_id', 'equipos.sede_id');
                });
        });
    }


    public function puedeSerGestionadoPor(User $user): bool
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        return DB::table('empresa_user')
            ->where('user_id', $user->id)
            ->where('empresa_id', $this->empresa_id)
            ->where(function ($q) {
                $q->whereNull('sede_id');
                if ($this->sede_id) {
                    $q->orWhere('sede_id', $this->sede_id);
                }
            })
            ->exists();
    }

    // Accessors

    public function getLabelCategoriaAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? ucfirst($this->categoria);
    }

    public function getLabelEstadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getColorEstadoAttribute(): string
    {
        return self::COLORES_ESTADO[$this->estado] ?? 'secondary';
    }

    public function getSchemaEspecificacionesAttribute(): array
    {
        return self::ESPECIFICACIONES_SCHEMA[$this->categoria] ?? [];
    }

    public function getLabelTipoEquipoAttribute(): string
    {
        return self::TIPOS_EQUIPO[$this->categoria] ?? 'Otro';
    }

    public function admiteMantenimiento(): bool
    {
        return in_array($this->categoria, self::CATEGORIAS_CON_MANTENIMIENTO, true);
    }

    /**
     * Filas sugeridas para "Unidades revisadas": componentes según las especificaciones
     * (procesador, RAM, disco...) y los periféricos vinculados.
     *
     * @return array<int, array{unidad: string, periferico_id: ?int}>
     */
    public function unidadesParaRevision(): array
    {
        $e = $this->especificaciones ?? [];
        $v = fn (string $k) => isset($e[$k]) && $e[$k] !== '' ? trim((string) $e[$k]) : null;

        $componentes = array_filter([
            $v('procesador') ? 'Procesador ' . $v('procesador') : null,
            $v('ram_gb') ? "RAM {$v('ram_gb')} GB" : null,
            $v('almacenamiento_gb') ? trim('Disco ' . ($v('tipo_disco') ?? '') . " {$v('almacenamiento_gb')} GB") : null,
            $v('tarjeta_grafica') ? 'Tarjeta gráfica ' . $v('tarjeta_grafica') : null,
            $v('pantalla_pulgadas') ? "Pantalla {$v('pantalla_pulgadas')}\"" : null,
            $v('estado_bateria') ? 'Batería (' . mb_strtolower($v('estado_bateria')) . ')' : null,
            $v('sistema_operativo') ? 'Sistema operativo ' . $v('sistema_operativo') : null,
        ]);

        $filas = array_map(fn ($u) => ['unidad' => $u, 'periferico_id' => null], array_values($componentes));

        foreach ($this->perifericos as $p) {
            $filas[] = ['unidad' => $p->descripcion, 'periferico_id' => $p->id];
        }

        return $filas;
    }

    public function tieneHistorial(): bool
    {
        return $this->mantenimientos()->exists();
    }

    public function razonesParaNoEliminar(): array
    {
        $razones = [];
        if ($this->mantenimientos()->exists()) {
            $razones[] = 'tiene servicios de mantenimiento registrados';
        }
        return $razones;
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
