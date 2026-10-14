<?php

namespace App\Exports\Inventario;

use App\Services\Reportes\InventarioReporteService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InventarioExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(private InventarioReporteService $servicio) {}

    public function sheets(): array
    {
        $hojas = [];

        if ($this->servicio->incluye('equipos')) {
            $hojas[] = new EquiposSheet($this->servicio->equipos());
        }

        if ($this->servicio->incluye('impresoras')) {
            $hojas[] = new ImpresorasSheet($this->servicio->impresoras());
        }

        return $hojas;
    }
}