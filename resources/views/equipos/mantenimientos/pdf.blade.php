{{-- resources/views/equipos/mantenimientos/pdf.blade.php --}}
{{-- Reporte "Contrato de prestación de servicio" (tamaño carta). dompdf: solo tablas y CSS simple. --}}
@use('App\Models\EquipoMantenimiento')
@php
    $m = $mantenimiento;
    $cliente = $equipo->empresa;
    $sede = $equipo->sede;
    $hora = fn (?string $h) => $h ? substr($h, 0, 5) : '';
    $servicio = trim($m->label_tipo . ($m->label_modalidad ? ' · ' . $m->label_modalidad : ''));
    $acciones = EquipoMantenimiento::ACCIONES;
    $fotos = $m->evidencias->groupBy('momento');
    $momentos = ['antes' => 'Antes del servicio', 'despues' => 'Después del servicio', 'otra' => 'Otras fotos'];
    $pie = array_filter([$empresa['direccion'], implode(' - ', array_filter([$empresa['telefono'] ? 'Tel. ' . $empresa['telefono'] : null, $empresa['correo'] ? 'Email: ' . $empresa['correo'] : null])), $empresa['ciudad']]);
    $filasUnidades = max(6, $m->unidades->count() + 1); // el formato deja filas libres
    $filasPartes = max(2, $m->partes->count());
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $formato['titulo'] }} {{ $m->numero_orden }}</title>
    <style>
        @page { margin: 1.1cm 1.2cm 2.2cm 1.2cm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.3px; color: #111; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: middle; }

        .titulo { font-size: 13px; font-weight: bold; color: #6b6b6b; line-height: 1.25; }
        .logo { max-height: 50px; max-width: 190px; }
        .empresa-nombre { font-size: 14px; font-weight: bold; color: #c0392b; }

        .gris { background: #dcdcdc; }
        .etq { color: #333; }
        .val { font-weight: bold; font-size: 9px; }
        .fila td { padding: 3px 4px; }

        .barra { background: #dcdcdc; font-weight: bold; font-size: 9.5px; padding: 3px 4px; }
        .caja { border: 1px solid #555; }
        .caja td { padding: 3.5px 6px; }

        .seccion { font-size: 10px; font-weight: bold; margin-top: 7px; }
        .area { border: 1px solid #555; padding: 4px 5px; min-height: 52px; white-space: pre-line; font-size: 8px; }

        .unidades th { background: #dcdcdc; font-weight: normal; font-size: 8.5px; padding: 2px 3px; text-align: center; border-bottom: 1.5px solid #333; }
        .unidades th.izq { text-align: left; }
        .unidades td { padding: 2px 3px; border-bottom: 1px solid #c8c8c8; height: 15px; font-size: 7.8px; }
        .casilla { display: inline-block; width: 13px; height: 12px; border: 1px solid #555; text-align: center; font-size: 9px; font-weight: bold; line-height: 12px; }
        .convenciones { font-size: 6.8px; color: #333; margin-top: 2px; }

        .partes th { font-weight: normal; font-size: 8.5px; text-align: left; padding: 2px 3px; border-bottom: 1px solid #333; }
        .partes th.gris { background: #dcdcdc; }
        .partes td { padding: 2px 3px; border-bottom: 1px solid #999; height: 14px; font-size: 8px; }

        .clausula { font-size: 7px; margin-top: 8px; }
        .firmas td { width: 50%; padding: 0 10px; vertical-align: bottom; }
        .firma-img { max-height: 48px; max-width: 200px; }
        .firma-linea { border-top: 1px solid #333; padding-top: 2px; font-size: 7.5px; }
        .firma-nombre { font-weight: bold; font-size: 8px; }

        .pie { position: fixed; bottom: -1.75cm; left: 0; right: 0; font-size: 7px; text-align: center; color: #222; }
        .pie .registro { text-align: left; margin-top: 4px; font-size: 6.8px; }

        .salto { page-break-before: always; }
        .fotos td { width: 50%; padding: 5px; text-align: center; page-break-inside: avoid; }
        .foto { max-width: 100%; max-height: 230px; border: 1px solid #ccc; }
        .foto-desc { font-size: 7px; color: #444; margin-top: 2px; }
    </style>
</head>
<body>

    {{-- Pie en todas las hojas --}}
    <div class="pie">
        @foreach ($pie as $linea)<div>{{ $linea }}</div>@endforeach
        <table class="registro"><tr>
            <td style="width: 50%;"><b>Elab. por:</b> {{ $m->registrador?->email ?? $m->registrador?->name }} {{ $m->created_at->format('d/m/Y H:i') }}</td>
            <td style="width: 50%;"><b>Dilig. por:</b> {{ $m->finalizador?->email ?? $m->finalizador?->name }} {{ $m->finalizado_at?->format('d/m/Y H:i') }}</td>
        </tr></table>
    </div>

    {{-- ── Encabezado ─────────────────────────────────────── --}}
    <table>
        <tr>
            <td class="titulo" style="width: 62%;">{{ $formato['titulo'] }}<br>{{ $formato['subtitulo'] }}</td>
            <td style="width: 38%; text-align: right;">
                @if ($logo)
                    <img src="{{ $logo }}" class="logo" alt="Logo">
                @else
                    <span class="empresa-nombre">{{ $empresa['nombre'] }}</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="fila" style="margin-top: 6px;">
        <tr>
            <td class="gris val" style="width: 14%;">Orden Nro</td>
            <td class="gris val" style="width: 17%;">{{ $m->numero_orden }}</td>
            <td class="etq" style="width: 16%;">Fecha de ingreso:</td>
            <td class="val" style="width: 18%;">{{ $m->fecha_ingreso?->format('d/m/Y') }}</td>
            <td style="width: 35%;"></td>
        </tr>
    </table>
    <table class="fila">
        <tr>
            <td class="etq" style="width: 14%;">Fecha de servicio:</td>
            <td class="val" style="width: 17%;">{{ $m->fecha_servicio->format('d/m/Y') }}</td>
            <td class="etq" style="width: 9%;">Hora inicial:</td>
            <td class="val" style="width: 8%;">{{ $hora($m->hora_inicio) }}</td>
            <td class="etq" style="width: 8%;">Hora final:</td>
            <td class="val" style="width: 8%;">{{ $hora($m->hora_fin) }}</td>
            <td class="etq" style="width: 14%;">Fecha de impresión:</td>
            <td class="val" style="width: 22%;">{{ $generado->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    {{-- ── Cliente / Equipo ───────────────────────────────── --}}
    <table style="margin-top: 5px;">
        <tr>
            <td class="barra" style="width: 48%;">Cliente</td>
            <td style="width: 2%;"></td>
            <td class="barra" style="width: 50%;">Equipo</td>
        </tr>
    </table>
    <table class="caja">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 2px 0;">
                <table>
                    @foreach ([
                        ['Nombre:', $cliente?->nombre], ['NIT:', $cliente?->nit], ['Ciudad:', $sede?->ciudad],
                        ['Dirección:', $sede?->direccion], ['Teléfono/Fax:', $cliente?->telefono], ['Email:', $cliente?->email],
                        ['Responsable:', $equipo->responsable?->name],
                    ] as [$e, $v])
                        <tr><td class="etq" style="width: 30%;">{{ $e }}</td><td class="val">{{ $v }}</td></tr>
                    @endforeach
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding: 2px 0;">
                <table>
                    @foreach ([
                        ['Equipo:', mb_strtoupper($equipo->label_categoria)], ['Marca:', $equipo->marca], ['Modelo:', $equipo->modelo],
                        ['Serial:', $equipo->numero_serie], ['Cód. Interno:', $equipo->codigo_interno],
                        ['Cont. Inicial:', $m->contador_inicial], ['Cont. Final:', $m->contador_final],
                    ] as [$e, $v])
                        <tr><td class="etq" style="width: 30%;">{{ $e }}</td><td class="val">{{ $v }}</td></tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- ── Servicio ───────────────────────────────────────── --}}
    <table class="fila" style="margin-top: 5px;">
        <tr>
            <td class="barra" style="width: 12%;">Servicio:</td>
            <td class="barra" style="width: 38%;">{{ $servicio }}</td>
            <td class="etq" style="width: 8%;">Depósito:</td>
            <td class="val" style="width: 15%;">{{ $m->deposito }}</td>
            <td class="etq" style="width: 13%;">Orden de trabajo:</td>
            <td class="val" style="width: 14%;">{{ $m->ticket ? 'Ticket #' . $m->ticket->id : '' }}</td>
        </tr>
        <tr>
            <td class="etq">Técnico/Ing.:</td>
            <td class="val">{{ mb_strtoupper($m->tecnico?->name ?? '') }}</td>
            <td class="etq">Estado:</td>
            <td class="val">{{ mb_strtoupper($m->label_estado) }}</td>
            <td class="etq">Tipo de equipo:</td>
            <td class="val">{{ $equipo->label_tipo_equipo }}</td>
        </tr>
    </table>

    {{-- ── Falla ──────────────────────────────────────────── --}}
    <div class="seccion">Falla:</div>
    <div class="area" style="min-height: 44px;">{{ $m->falla }}</div>

    {{-- ── Unidades revisadas ─────────────────────────────── --}}
    <div class="seccion">Unidades revisadas</div>
    <table class="unidades">
        <tr>
            <th class="izq" style="width: 40%;">Unidad</th>
            @foreach ($acciones as [$encabezado])<th style="width: 10%;">{{ $encabezado }}</th>@endforeach
        </tr>
        @for ($i = 0; $i < $filasUnidades; $i++)
            @php $u = $m->unidades[$i] ?? null; @endphp
            <tr>
                <td>{{ $u?->unidad }}</td>
                @foreach (array_keys($acciones) as $accion)
                    <td style="text-align: center;"><span class="casilla">{{ $u?->{$accion} ? 'X' : '' }}</span></td>
                @endforeach
            </tr>
        @endfor
    </table>
    <div class="convenciones">Convenciones: L Limpieza, A Ajuste, Lu Lubricación, C/I Cambio o Instalación, Rev. Revisión, Repar. Reparación</div>

    {{-- ── Observaciones ──────────────────────────────────── --}}
    <div class="seccion">Observaciones:</div>
    <div class="area" style="min-height: 58px;">{{ $m->observaciones }}</div>

    {{-- ── Solicitud y cambio de partes ───────────────────── --}}
    <div class="seccion">Solicitud y cambio de partes</div>
    <table class="partes">
        <tr>
            <th class="gris" style="width: 22%;">Nro de parte</th>
            <th style="width: 34%;">Nombre de parte</th>
            <th class="gris" style="width: 22%;">Nro de serie original</th>
            <th class="gris" style="width: 22%;">Nro de serie remplazo</th>
        </tr>
        @for ($i = 0; $i < $filasPartes; $i++)
            @php $p = $m->partes[$i] ?? null; @endphp
            <tr><td>{{ $p?->numero_parte }}</td><td>{{ $p?->nombre }}</td><td>{{ $p?->serial_original }}</td><td>{{ $p?->serial_reemplazo }}</td></tr>
        @endfor
    </table>

    @if ($formato['clausula'])<div class="clausula">{{ $formato['clausula'] }}</div>@endif

    {{-- ── Firmas ─────────────────────────────────────────── --}}
    <table class="firmas" style="margin-top: 8px; page-break-inside: avoid;">
        <tr>
            <td style="height: 52px; text-align: center;">@if ($src = $imagen($m->firma_cliente))<img src="{{ $src }}" class="firma-img" alt="">@endif</td>
            <td style="height: 52px; text-align: center;">@if ($src = $imagen($m->firma_tecnico))<img src="{{ $src }}" class="firma-img" alt="">@endif</td>
        </tr>
        <tr>
            <td>
                <div class="firma-linea">Firma cliente salida:
                    <span class="firma-nombre">{{ $m->cliente_nombre }}</span>
                    {{ collect([$m->cliente_cargo, $m->cliente_documento ? 'Doc. ' . $m->cliente_documento : null])->filter()->implode(' · ') }}
                </div>
            </td>
            <td>
                <div class="firma-linea">Firma Ing./Técnico: <span class="firma-nombre">{{ $m->tecnico?->name }}</span></div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 6px;">
        <tr>
            <td style="width: 9%; font-size: 10px; font-weight: bold;">Fotos:</td>
            <td style="font-size: 8.5px;">
                {{ $m->evidencias->count() ? $m->evidencias->count() . ' fotos en las hojas siguientes · ' : '' }}{{ route('equipos.mantenimientos.show', [$equipo, $m]) }}
            </td>
        </tr>
    </table>

    {{-- ── Registro fotográfico ───────────────────────────── --}}
    @if ($m->evidencias->isNotEmpty())
        <div class="salto"></div>
        <table class="fila">
            <tr>
                <td class="titulo" style="width: 60%; font-size: 11px;">REGISTRO FOTOGRÁFICO</td>
                <td class="gris val" style="width: 14%;">Orden Nro</td>
                <td class="gris val" style="width: 26%;">{{ $m->numero_orden }} · {{ $m->fecha_servicio->format('d/m/Y') }}</td>
            </tr>
        </table>
        <div style="font-size: 8px; margin: 3px 0 4px;">{{ $equipo->label_categoria }} {{ $equipo->marca }} {{ $equipo->modelo }} · Serial {{ $equipo->numero_serie ?? '—' }} · {{ $cliente?->nombre }}</div>

        @foreach ($momentos as $clave => $titulo)
            @continue(! $fotos->has($clave))
            @foreach ($fotos[$clave]->chunk(2) as $fila)
                <table class="fotos" style="page-break-inside: avoid;">
                    @if ($loop->first)
                        <tr><td colspan="2" class="barra" style="text-align: left;">{{ $titulo }}</td></tr>
                    @endif
                    <tr>
                        @foreach ($fila as $foto)
                            <td>
                                @if ($src = $imagen($foto->archivo))<img src="{{ $src }}" class="foto" alt="">@endif
                                @if ($foto->descripcion)<div class="foto-desc">{{ $foto->descripcion }}</div>@endif
                            </td>
                        @endforeach
                        @if ($fila->count() === 1)<td></td>@endif
                    </tr>
                </table>
            @endforeach
        @endforeach
    @endif

</body>
</html>
