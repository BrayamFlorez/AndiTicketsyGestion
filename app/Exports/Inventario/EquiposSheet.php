<?php

namespace App\Exports\Inventario;

use App\Models\Equipo;
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

class EquiposSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public function __construct(private Collection $equipos) {}

    public function collection(): Collection
    {
        return $this->equipos;
    }

    public function title(): string
    {
        return 'Equipos';
    }

    public function headings(): array
    {
        return [
            'Empresa', 'Sede', 'Categoría', 'Nombre', 'Marca', 'Modelo', 'N° de serie',
            'Ubicación', 'Estado', 'Responsable', 'Especificaciones', 'Notas', 'Registrado',
        ];
    }

    /** @param Equipo $equipo */
    public function map($equipo): array
    {
        return [
            $equipo->empresa?->nombre ?? '—',
            $equipo->sede?->nombre ?? 'Sin sede',
            $equipo->label_categoria,
            $equipo->nombre,
            $equipo->marca,
            $equipo->modelo,
            $equipo->numero_serie,
            $equipo->ubicacion,
            $equipo->label_estado,
            $equipo->responsable?->name,
            InventarioReporteService::resumenEspecificaciones($equipo),
            $equipo->notas,
            $equipo->created_at?->format('d/m/Y'),
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

                // Columna de especificaciones: ancho fijo con ajuste de texto
                $hoja->getColumnDimension('K')->setAutoSize(false)->setWidth(60);
                $hoja->getStyle('K:K')->getAlignment()->setWrapText(true);
            },
        ];
    }
}