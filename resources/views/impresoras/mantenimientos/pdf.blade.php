{{-- resources/views/impresoras/mantenimientos/pdf.blade.php --}}
{{-- Reporte de mantenimiento, tamaño carta (dompdf: solo tablas y CSS simple, sin flex ni grid) --}}
@php
    $evidencias = $mantenimiento->evidencias->groupBy('momento');
    $momentos = ['antes' => 'Antes del mantenimiento', 'despues' => 'Después del mantenimiento', 'otra' => 'Otras evidencias'];
    $numero = fn ($n) => $n !== null ? number_format($n, 0, ',', '.') : '—';
    $datosEmpresa = array_filter([
        $empresa['nit'] ? 'NIT ' . $empresa['nit'] : null,
        $empresa['direccion'], $empresa['telefono'], $empresa['correo'], $empresa['sitio_web'],
    ]);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimiento #{{ $mantenimiento->id }} — {{ $impresora->codigo }}</title>
    <style>
        @page { margin: 1.4cm 1.5cm 1.7cm 1.5cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1f2937; line-height: 1.4; }

        .pie { position: fixed; bottom: -1.1cm; left: 0; right: 0; font-size: 7.5px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 4px; }

        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }

        .encabezado td { vertical-align: middle; }
        .logo { max-height: 52px; max-width: 170px; }
        .empresa-nombre { font-size: 13px; font-weight: bold; color: #111827; }
        .empresa-datos { font-size: 8px; color: #6b7280; }
        .titulo-caja { border: 1.5px solid #3c50e0; border-radius: 4px; padding: 6px 8px; text-align: center; }
        .titulo { font-size: 11px; font-weight: bold; color: #3c50e0; letter-spacing: .5px; }
        .numero { font-size: 15px; font-weight: bold; color: #111827; }
        .fecha { font-size: 8px; color: #6b7280; }

        .seccion { margin-top: 12px; page-break-inside: avoid; }
        .seccion-titulo { background: #1c2434; color: #fff; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .6px; padding: 4px 8px; border-radius: 3px; }

        .datos td { padding: 4px 6px; border-bottom: 1px solid #eef0f4; }
        .datos .etiqueta { width: 17%; font-size: 7.5px; font-weight: bold; text-transform: uppercase; color: #6b7280; }
        .datos .valor { width: 33%; font-weight: bold; color: #111827; }

        .texto { margin-top: 5px; padding: 7px 9px; background: #f7f9fc; border-left: 3px solid #3c50e0; white-space: pre-line; }
        .texto-suave { margin-top: 4px; color: #4b5563; font-style: italic; white-space: pre-line; }

        .tabla th { background: #f1f5f9; font-size: 7.5px; text-transform: uppercase; color: #4b5563; text-align: left; padding: 4px 6px; border: 1px solid #e2e8f0; }
        .tabla td { padding: 4px 6px; border: 1px solid #e2e8f0; }

        .momento { font-size: 8.5px; font-weight: bold; color: #3c50e0; text-transform: uppercase; }
        .fotos td { width: 50%; padding: 5px; text-align: center; page-break-inside: avoid; }
        .foto { max-width: 100%; max-height: 215px; border: 1px solid #e2e8f0; border-radius: 3px; }
        .foto-desc { font-size: 7.5px; color: #4b5563; margin-top: 2px; }

        .conformidad { font-size: 8.5px; color: #4b5563; margin-top: 6px; }
        .firmas td { width: 50%; padding: 6px 14px; text-align: center; }
        .firma-caja { height: 78px; }
        .firma { max-height: 74px; max-width: 220px; }
        .firma-linea { border-top: 1px solid #111827; margin-top: 2px; padding-top: 3px; }
        .firma-nombre { font-weight: bold; font-size: 9px; color: #111827; }
        .firma-detalle { font-size: 7.5px; color: #6b7280; }
    </style>
</head>
<body>

    <div class="pie">
        {{-- "Página X de Y" lo escribe el controlador (dompdf no conoce el total de páginas en CSS) --}}
        {{ $empresa['nombre'] }} · Reporte de mantenimiento #{{ $mantenimiento->id }} · Generado el {{ $generado->format('d/m/Y H:i') }}
    </div>

    {{-- ── Encabezado ─────────────────────────────────────── --}}
    <table class="encabezado">
        <tr>
            <td style="width: 62%;">
                <table><tr>
                    @if ($logo)
                        <td style="width: 1%; padding-right: 10px;"><img src="{{ $logo }}" class="logo" alt="Logo"></td>
                    @endif
                    <td>
                        <div class="empresa-nombre">{{ $empresa['nombre'] }}</div>
                        @if ($datosEmpresa)<div class="empresa-datos">{{ implode(' · ', $datosEmpresa) }}</div>@endif
                    </td>
                </tr></table>
            </td>
            <td style="width: 38%;">
                <div class="titulo-caja">
                    <div class="titulo">REPORTE DE MANTENIMIENTO</div>
                    <div class="numero">N.º {{ str_pad($mantenimiento->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="fecha">{{ $mantenimiento->label_tipo }} · {{ $mantenimiento->fecha_realizado->format('d/m/Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Cliente y equipo ───────────────────────────────── --}}
    <div class="seccion">
        <div class="seccion-titulo">Cliente y equipo</div>
        <table class="datos">
            <tr>
                <td class="etiqueta">Cliente</td><td class="valor">{{ $impresora->empresa?->nombre ?? '—' }}</td>
                <td class="etiqueta">Sede</td><td class="valor">{{ $impresora->sede?->nombre ?? '—' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Impresora</td><td class="valor">{{ $impresora->nombre_visible }}</td>
                <td class="etiqueta">Código</td><td class="valor">{{ $impresora->codigo }}{{ $impresora->codigo_interno ? ' · #' . $impresora->codigo_interno : '' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Modelo</td><td class="valor">{{ $impresora->modelo?->nombre_completo ?? '—' }}</td>
                <td class="etiqueta">N.º de serie</td><td class="valor">{{ $impresora->numero_serie ?? '—' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Ubicación</td><td class="valor">{{ $impresora->ubicacion_fisica ?? '—' }}</td>
                <td class="etiqueta">Dirección IP</td><td class="valor">{{ $impresora->ip_red ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ── Trabajo realizado ──────────────────────────────── --}}
    <div class="seccion">
        <div class="seccion-titulo">Trabajo realizado</div>
        <table class="datos">
            <tr>
                <td class="etiqueta">Tipo</td><td class="valor">{{ $mantenimiento->label_tipo }}</td>
                <td class="etiqueta">Fecha</td><td class="valor">{{ $mantenimiento->fecha_realizado->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Técnico</td><td class="valor">{{ $mantenimiento->realizador?->name ?? '—' }}</td>
                <td class="etiqueta">Próximo</td><td class="valor">{{ $mantenimiento->proximo_programado?->format('d/m/Y') ?? 'Sin programar' }}</td>
            </tr>
        </table>
        <div class="texto">{{ $mantenimiento->descripcion }}</div>
        @if ($mantenimiento->observaciones)
            <div class="texto-suave">Observaciones: {{ $mantenimiento->observaciones }}</div>
        @endif
    </div>

    {{-- ── Contador y consumibles ─────────────────────────── --}}
    @if ($mantenimiento->contador_paginas !== null || $mantenimiento->consumiblesInstalados->isNotEmpty())
        <div class="seccion">
            <div class="seccion-titulo">Contador y consumibles</div>
            @if ($mantenimiento->contador_paginas !== null)
                <table class="datos">
                    <tr>
                        <td class="etiqueta">Contador</td><td class="valor">{{ $numero($mantenimiento->contador_paginas) }} páginas</td>
                        <td class="etiqueta">Desde la lectura anterior</td>
                        <td class="valor">
                            @if ($mantenimiento->lecturaContador?->reinicio)
                                Contador reiniciado
                            @else
                                {{ $mantenimiento->lecturaContador?->paginas_desde_anterior !== null ? '+' . $numero($mantenimiento->lecturaContador->paginas_desde_anterior) . ' páginas' : '—' }}
                            @endif
                        </td>
                    </tr>
                </table>
            @endif

            @if ($mantenimiento->consumiblesInstalados->isNotEmpty())
                <table class="tabla" style="margin-top: 6px;">
                    <tr><th>Consumible instalado</th><th>Instalado</th><th>Duración estimada</th></tr>
                    @foreach ($mantenimiento->consumiblesInstalados as $c)
                        <tr>
                            <td>{{ $c->descripcion_corta }}</td>
                            <td>{{ $c->fecha_instalacion?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $c->dias_estimados }} días</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @endif

    {{-- ── Evidencia fotográfica ──────────────────────────── --}}
    @if ($mantenimiento->evidencias->isNotEmpty())
        <div class="seccion" style="page-break-inside: auto;">
            <div class="seccion-titulo">Evidencia fotográfica ({{ $mantenimiento->evidencias->count() }})</div>
            @foreach ($momentos as $clave => $titulo)
                @continue(! $evidencias->has($clave))
                {{-- Una tabla por fila de fotos: el título nunca queda solo al final de una página --}}
                @foreach ($evidencias[$clave]->chunk(2) as $fila)
                    <table class="fotos" style="page-break-inside: avoid;">
                        @if ($loop->first)
                            <tr><td colspan="2" class="momento" style="text-align: left; padding: 6px 0 0;">{{ $titulo }}</td></tr>
                        @endif
                        <tr>
                            @foreach ($fila as $foto)
                                <td>
                                    @if ($src = $imagen($foto->archivo))
                                        <img src="{{ $src }}" class="foto" alt="">
                                    @endif
                                    @if ($foto->descripcion)<div class="foto-desc">{{ $foto->descripcion }}</div>@endif
                                </td>
                            @endforeach
                            @if ($fila->count() === 1)<td></td>@endif
                        </tr>
                    </table>
                @endforeach
            @endforeach
        </div>
    @endif

    {{-- ── Conformidad y firmas ───────────────────────────── --}}
    <div class="seccion">
        <div class="seccion-titulo">Conformidad</div>
        @if ($mantenimiento->esRegistroSinFirmas())
            <p class="conformidad">Registro anterior a la firma digital: este mantenimiento no tiene firmas.</p>
        @else
            <p class="conformidad">
                Con su firma, el cliente declara que recibió el servicio descrito en este reporte.
            </p>
            <table class="firmas" style="margin-top: 8px;">
                <tr>
                    <td>
                        <div class="firma-caja">@if ($src = $imagen($mantenimiento->firma_tecnico))<img src="{{ $src }}" class="firma" alt="">@endif</div>
                        <div class="firma-linea">
                            <div class="firma-nombre">{{ $mantenimiento->realizador?->name ?? '—' }}</div>
                            <div class="firma-detalle">Técnico · {{ $empresa['nombre'] }}</div>
                            <div class="firma-detalle">{{ $mantenimiento->firma_tecnico_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="firma-caja">@if ($src = $imagen($mantenimiento->firma_cliente))<img src="{{ $src }}" class="firma" alt="">@endif</div>
                        <div class="firma-linea">
                            <div class="firma-nombre">{{ $mantenimiento->cliente_nombre }}</div>
                            <div class="firma-detalle">
                                {{ collect([$mantenimiento->cliente_cargo, $mantenimiento->cliente_documento ? 'Doc. ' . $mantenimiento->cliente_documento : null])->filter()->implode(' · ') ?: 'Cliente' }}
                                · {{ $impresora->empresa?->nombre }}
                            </div>
                            <div class="firma-detalle">{{ $mantenimiento->firma_cliente_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        @endif
    </div>

</body>
</html>
