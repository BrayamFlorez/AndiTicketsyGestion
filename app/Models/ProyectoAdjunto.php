<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ProyectoAdjunto extends Model
{
    protected $table = 'proyecto_adjuntos';

    protected $fillable = [
        'proyecto_id',
        'adjuntable_type',
        'adjuntable_id',
        'tipo',
        'nombre_original',
        'ruta',
        'mime',
        'tamano',
        'subido_por',
    ];

    protected $casts = [
        'tamano'      => 'integer',
        'proyecto_id' => 'integer',
        'subido_por'  => 'integer',
    ];

    /** Disco privado (en Laravel 11 'local' apunta a storage/app/private) */
    public const DISCO = 'local';

    /** Límite por archivo en KB (20 MB) */
    public const MAX_KB = 20480;

    /** Máximo de archivos por envío */
    public const MAX_ARCHIVOS = 10;

    public const EXTENSIONES = [
        'imagen'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic', 'heif'],
        'audio'     => ['mp3', 'ogg', 'oga', 'opus', 'wav', 'm4a', 'aac', 'weba', 'amr'],
        'video'     => ['mp4', 'mov', 'avi', 'mkv', 'webm', '3gp', 'm4v'],
        'documento' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt', 'odt', 'ods', 'zip', 'rar'],
    ];

    // ── Relaciones ──────────────────────────────────────────────────────

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function adjuntable(): MorphTo
    {
        return $this->morphTo();
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por')->withTrashed();
    }

    // ── Eventos ─────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        // Al borrar el registro se elimina el archivo físico
        static::deleted(function (ProyectoAdjunto $adjunto) {
            Storage::disk(self::DISCO)->delete($adjunto->ruta);
        });
    }

    // ── Helpers estáticos ───────────────────────────────────────────────

    public static function extensionesPermitidas(): array
    {
        return array_merge(...array_values(self::EXTENSIONES));
    }

    /** Reglas de validación compartidas por etapas y solicitudes */
    public static function reglas(): array
    {
        return [
            'archivos'   => ['nullable', 'array', 'max:' . self::MAX_ARCHIVOS],
            'archivos.*' => ['file', 'max:' . self::MAX_KB, 'extensions:' . implode(',', self::extensionesPermitidas())],
        ];
    }

    /** Clasifica un archivo por su MIME y, si no es concluyente, por su extensión */
    public static function detectarTipo(?string $mime, string $extension): string
    {
        $mime = (string) $mime;

        foreach (['image/' => 'imagen', 'audio/' => 'audio', 'video/' => 'video'] as $prefijo => $tipo) {
            if (str_starts_with($mime, $prefijo)) {
                return $tipo;
            }
        }

        foreach (self::EXTENSIONES as $tipo => $extensiones) {
            if (in_array(strtolower($extension), $extensiones, true)) {
                return $tipo;
            }
        }

        return 'documento';
    }

    // ── Accesores ───────────────────────────────────────────────────────

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->nombre_original, PATHINFO_EXTENSION));
    }

    public function getPesoLegibleAttribute(): string
    {
        $mb = 1024 * 1024;

        return $this->tamano >= $mb
            ? number_format($this->tamano / $mb, 1, ',', '.') . ' MB'
            : max(1, (int) round($this->tamano / 1024)) . ' KB';
    }

    public function getIconoAttribute(): string
    {
        return match (true) {
            $this->tipo === 'imagen'                                  => 'fa-image',
            $this->tipo === 'audio'                                   => 'fa-microphone',
            $this->tipo === 'video'                                   => 'fa-video',
            $this->extension === 'pdf'                                => 'fa-file-pdf',
            in_array($this->extension, ['doc', 'docx', 'odt'], true)  => 'fa-file-word',
            in_array($this->extension, ['xls', 'xlsx', 'csv', 'ods'], true) => 'fa-file-excel',
            in_array($this->extension, ['ppt', 'pptx'], true)         => 'fa-file-powerpoint',
            in_array($this->extension, ['zip', 'rar'], true)          => 'fa-file-zipper',
            default                                                   => 'fa-file-lines',
        };
    }

    public function getUrlAttribute(): string
    {
        return route('proyectos.adjuntos.show', [$this->proyecto_id, $this->id]);
    }

    public function getUrlDescargaAttribute(): string
    {
        return route('proyectos.adjuntos.show', [$this->proyecto_id, $this->id, 'descargar' => 1]);
    }
}
