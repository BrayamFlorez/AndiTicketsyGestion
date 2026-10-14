<?php

namespace App\Http\Controllers;

use App\Exports\Inventario\InventarioExport;
use App\Http\Requests\ReporteInventarioRequest;
use App\Models\Equipo;
use App\Models\Sede;
use App\Services\Reportes\InventarioReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteInventarioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Pantalla de filtros
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('reportes.inventario'), 403);

        $empresas = InventarioReporteService::empresasDisponiblesPara($request->user());

        return view('reportes.inventario.index', [
            'empresas'        => $empresas,
            'sedesPorEmpresa' => Sede::activas()
                ->whereIn('empresa_id', $empresas->pluck('id'))
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'empresa_id'])
                ->groupBy('empresa_id'),
            'categorias'      => Equipo::CATEGORIAS,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXCEL — Listado plano, una hoja por tipo de inventario
    |--------------------------------------------------------------------------
    */
    public function excel(ReporteInventarioRequest $request): BinaryFileResponse
    {
        $servicio = InventarioReporteService::para($request->user(), $request->filtros());

        return Excel::download(new InventarioExport($servicio), $this->nombreArchivo('xlsx'));
    }

    /*
    |--------------------------------------------------------------------------
    | PDF — Informe con KPIs, gráficos y detalle agrupado por empresa
    |--------------------------------------------------------------------------
    */
    public function pdf(ReporteInventarioRequest $request): Response
    {
        $servicio = InventarioReporteService::para($request->user(), $request->filtros());

        $pdf = Pdf::loadView('reportes.inventario.pdf', [
            'equipos'           => $servicio->equipos(),
            'impresoras'        => $servicio->impresoras(),
            'resumen'           => $servicio->resumen(),
            'filtros'           => $servicio->descripcionFiltros(),
            'incluyeEquipos'    => $servicio->incluye('equipos'),
            'incluyeImpresoras' => $servicio->incluye('impresoras'),
            'generadoPor'       => $request->user()->name,
            'generadoEn'        => now(),
        ])->setPaper('a4', 'landscape');

        // Pie de página con numeración (requiere renderizar antes)
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $fuente = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(34, 572, 'Peñaranda Digital · Reporte de inventario', $fuente, 7, [0.45, 0.5, 0.56]);
        $canvas->page_text(740, 572, 'Página {PAGE_NUM} de {PAGE_COUNT}', $fuente, 7, [0.45, 0.5, 0.56]);

        return $pdf->download($this->nombreArchivo('pdf'));
    }

    private function nombreArchivo(string $extension): string
    {
        return 'inventario_' . now()->format('Y-m-d_Hi') . '.' . $extension;
    }
}