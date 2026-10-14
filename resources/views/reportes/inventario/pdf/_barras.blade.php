{{-- Recibe: $titulo, $datos [clave => total], $labels [clave => texto], $colores [clave => hex] --}}
@php
    $maximo = $datos ? max($datos) : 0;
    $total  = array_sum($datos);
@endphp

<div class="chart">
    <div class="chart-title">{{ $titulo }}</div>

    @if (empty($datos))
        <div class="vacio">Sin datos para los filtros aplicados.</div>
    @else
        <table class="bars">
            @foreach ($datos as $clave => $cantidad)
                @php
                    $ancho = $maximo ? max(2, round($cantidad / $maximo * 100)) : 0;
                    $share = $total ? round($cantidad / $total * 100) : 0;
                @endphp
                <tr>
                    <td class="bar-label">{{ $labels[$clave] ?? ucfirst(str_replace('_', ' ', $clave)) }}</td>
                    <td class="bar-cell">
                        <div class="track">
                            <div class="fill" style="width: {{ $ancho }}%; background: {{ $colores[$clave] ?? '#007acc' }};"></div>
                        </div>
                    </td>
                    <td class="bar-value">{{ $cantidad }} <span>({{ $share }}%)</span></td>
                </tr>
            @endforeach
        </table>
    @endif
</div>