<?php

namespace App\Enums;

/**
 * Vocabulario cerrado de acciones auditables sobre un documento (WorkflowInstance)
 * o sus adjuntos. Es la ÚNICA fuente de verdad de "qué se puede auditar": para
 * agregar una acción nueva, se agrega un case aquí y se mapea en
 * App\Listeners\Auditing\DocumentAuditSubscriber (o en el observer correspondiente).
 */
enum DocumentAuditAction: string
{
    case CREADO                = 'creado';
    case VISUALIZADO           = 'visualizado';
    case DESCARGADO            = 'descargado';
    case ADJUNTO_SUBIDO        = 'adjunto_subido';
    case ADJUNTO_ELIMINADO     = 'adjunto_eliminado';
    case APROBADO              = 'aprobado';
    case RECHAZADO             = 'rechazado';
    case DEVUELTO              = 'devuelto';
    case ETAPA_AVANZADA        = 'etapa_avanzada';
    case CORRECCION_SOLICITADA = 'correccion_solicitada';
    case CORRECCION_REENVIADA  = 'correccion_reenviada';
    case FLUJO_FINALIZADO      = 'flujo_finalizado';
    case FLUJO_CANCELADO       = 'flujo_cancelado';
    case FLUJO_REABIERTO       = 'flujo_reabierto';
    case EDITADO               = 'editado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::CREADO                => 'Documento creado',
            self::VISUALIZADO           => 'Documento visualizado',
            self::DESCARGADO            => 'Adjunto descargado',
            self::ADJUNTO_SUBIDO        => 'Adjunto subido',
            self::ADJUNTO_ELIMINADO     => 'Adjunto eliminado',
            self::APROBADO              => 'Etapa aprobada',
            self::RECHAZADO             => 'Etapa rechazada',
            self::DEVUELTO              => 'Etapa devuelta',
            self::ETAPA_AVANZADA        => 'Etapa avanzada',
            self::CORRECCION_SOLICITADA => 'Corrección solicitada',
            self::CORRECCION_REENVIADA  => 'Corrección reenviada',
            self::FLUJO_FINALIZADO      => 'Flujo finalizado (aprobado)',
            self::FLUJO_CANCELADO       => 'Flujo cancelado',
            self::FLUJO_REABIERTO       => 'Flujo reabierto',
            self::EDITADO               => 'Documento editado',
        };
    }

    public function icono(): string
    {
        return match ($this) {
            self::CREADO                => 'fa-plus-circle',
            self::VISUALIZADO           => 'fa-eye',
            self::DESCARGADO            => 'fa-download',
            self::ADJUNTO_SUBIDO        => 'fa-paperclip',
            self::ADJUNTO_ELIMINADO     => 'fa-trash-alt',
            self::APROBADO              => 'fa-check-circle',
            self::RECHAZADO             => 'fa-times-circle',
            self::DEVUELTO              => 'fa-undo',
            self::ETAPA_AVANZADA        => 'fa-forward',
            self::CORRECCION_SOLICITADA => 'fa-exclamation-triangle',
            self::CORRECCION_REENVIADA  => 'fa-redo',
            self::FLUJO_FINALIZADO      => 'fa-flag-checkered',
            self::FLUJO_CANCELADO       => 'fa-ban',
            self::FLUJO_REABIERTO       => 'fa-lock-open',
            self::EDITADO               => 'fa-edit',
        };
    }

    /**
     * Reutiliza las clases .db-badge.* que YA existen en public/css/dashboard.css
     * (abierto, asignado, en_progreso, resuelto, cancelado, critica, media...)
     * para no tener que tocar ningún archivo CSS.
     */
    public function claseBadge(): string
    {
        return match ($this) {
            self::APROBADO, self::FLUJO_FINALIZADO, self::ADJUNTO_SUBIDO     => 'resuelto',
            self::RECHAZADO, self::FLUJO_CANCELADO, self::ADJUNTO_ELIMINADO  => 'cancelado',
            self::DEVUELTO, self::CORRECCION_SOLICITADA                     => 'critica',
            self::CORRECCION_REENVIADA, self::FLUJO_REABIERTO,
            self::ETAPA_AVANZADA                                            => 'en_progreso',
            self::VISUALIZADO, self::DESCARGADO                             => 'abierto',
            self::CREADO                                                    => 'asignado',
            self::EDITADO                                                   => 'media',
        };
    }
}
