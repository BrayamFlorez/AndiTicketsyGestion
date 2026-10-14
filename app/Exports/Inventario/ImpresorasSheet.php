<?php

namespace App\Exports\Inventario;

use App\Models\Impresora;
use App\Models\ImpresoraModelo;
use App\Services\Reportes\InventarioReporteService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImpresorasSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public function __construct(private Collection $impresoras) {}

    public function collection(): Collection
    {
        return $this->impresoras;
    }

    public function title(): string
    {
        return 'Impresoras';
    }

    public function headings(): array
    {
        return [
            'Código', 'Código interno', 'Nombre', 'Empresa', 'Sede', 'Ubicación',
            'Modelo', 'Tecnología', 'Color', 'N° de serie', 'IP', 'Hostname', 'MAC',
            'Estado', 'Contador de páginas', 'Consumibles (nivel estimado)',
            'Consumibles en alerta', 'Próximo mantenimiento', 'Fecha de adquisición',
        ];
    }

    /** @param Impresora $impresora */
    public function map($impresora): array
    {
        $alertas = $impresora->consumiblesActivos
            ->filter(fn ($c) => in_array($c->estado_nivel, ['critico', 'agotado'], true))
            ->count();

        return [
            $impresora->codigo,
            $impresora->codigo_interno,
            $impresora->nombre_visible,
            $impresora->empresa?->nombre ?? '—',
            $impresora->sede?->nombre ?? 'Sin sede',
            $impresora->ubicacion_fisica,
            $impresora->modelo?->nombre_completo ?? 'Sin modelo',
            $impresora->modelo
                ? (ImpresoraModelo::TIPOS_TECNOLOGIA[$impresora->modelo->tipo_tecnologia] ?? $impresora->modelo->tipo_tecnologia)
                : '—',
            $impresora->modelo ? ($impresora->modelo->color ? 'Color' : 'Monocromática') : '—',
            $impresora->numero_serie,
            $impresora->ip_red,
            $impresora->hostname,
            $impresora->mac_address,
            $impresora->label_estado,
            $impresora->contador_paginas_actual,
            InventarioReporteService::resumenConsumibles($impresora),
            $alertas,
            $impresora->proximo_mantenimiento_sugerido?->format('d/m/Y'),
            $impresora->fecha_adquisicion?->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007ACC']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $hoja = $event->sheet->getDelegate();
                $hoja->freezePane('A2');
                $hoja->setAutoFilter($hoja->calculateWorksheetDimension());

                // Columna de consumibles: ancho fijo con ajuste de texto
                $hoja->getColumnDimension('P')->setAutoSize(false)->setWidth(55);
                $hoja->getStyle('P:P')->getAlignment()->setWrapText(true);
            },
        ];
    }
}