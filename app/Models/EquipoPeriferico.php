<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Periférico vinculado a un PC: pantalla, teclado, mouse, UPS... */
class EquipoPeriferico extends Model
{
    protected $table = 'equipo_perifericos';

    /** tipo => [etiqueta, icono] */
    public const TIPOS = [
        'monitor'    => ['Monitor / Pantalla', 'fa-desktop'],
        'teclado'    => ['Teclado', 'fa-keyboard'],
        'mouse'      => ['Mouse', 'fa-mouse'],
        'ups'        => ['UPS / Regulador', 'fa-car-battery'],
        'parlantes'  => ['Parlantes', 'fa-volume-up'],
        'diadema'    => ['Diadema / Audífonos', 'fa-headset'],
        'camara'     => ['Cámara web', 'fa-video'],
        'lector'     => ['Lector / Escáner', 'fa-barcode'],
        'docking'    => ['Docking / Base', 'fa-plug'],
        'otro'       => ['Otro', 'fa-puzzle-piece'],
    ];

    protected $fillable = ['equipo_id', 'tipo', 'marca', 'modelo', 'numero_serie', 'notas'];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function getLabelTipoAttribute(): string
    {
        return self::TIPOS[$this->tipo][0] ?? ucfirst($this->tipo);
    }

    public function getIconoAttribute(): string
    {
        return self::TIPOS[$this->tipo][1] ?? 'fa-puzzle-piece';
    }

    /** "Mouse HP MCA4T8": como se lista en "Unidades revisadas" */
    public function getDescripcionAttribute(): string
    {
        $tipo = $this->tipo === 'otro' && $this->notas ? $this->notas : (self::TIPOS[$this->tipo][0] ?? $this->tipo);
        $tipo = explode(' / ', $tipo)[0];

        return trim(implode(' ', array_filter([$tipo, $this->marca, $this->modelo])));
    }
}
