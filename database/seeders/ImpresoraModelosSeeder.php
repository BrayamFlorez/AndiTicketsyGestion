<?php

namespace Database\Seeders;

use App\Models\ImpresoraModelo;
use Illuminate\Database\Seeder;

class ImpresoraModelosSeeder extends Seeder
{
    private const CONSUMIBLES_LASER_MONO = [
        ['tipo' => 'toner',  'color' => 'negro'],
        ['tipo' => 'tambor', 'color' => null],
    ];

    private const CONSUMIBLES_LASER_COLOR = [
        ['tipo' => 'toner',  'color' => 'negro'],
        ['tipo' => 'toner',  'color' => 'cyan'],
        ['tipo' => 'toner',  'color' => 'magenta'],
        ['tipo' => 'toner',  'color' => 'amarillo'],
        ['tipo' => 'tambor', 'color' => null],
    ];

    private const CONSUMIBLES_INYECCION_MONO = [
        ['tipo' => 'cartucho', 'color' => 'negro'],
    ];

    private const CONSUMIBLES_INYECCION_COLOR = [
        ['tipo' => 'cartucho', 'color' => 'negro'],
        ['tipo' => 'cartucho', 'color' => 'cyan'],
        ['tipo' => 'cartucho', 'color' => 'magenta'],
        ['tipo' => 'cartucho', 'color' => 'amarillo'],
    ];

    private const MODELOS = [
        ['HP',      'LaserJet Pro M404dn',     'laser',     false, false, self::CONSUMIBLES_LASER_MONO],
        ['HP',      'LaserJet MFP M283fdw',    'laser',     true,  true,  self::CONSUMIBLES_LASER_COLOR],
        ['HP',      'DeskJet 2775',            'inyeccion', true,  true,  self::CONSUMIBLES_INYECCION_COLOR],
        ['Epson',   'EcoTank L3250',           'inyeccion', true,  true,  self::CONSUMIBLES_INYECCION_COLOR],
        ['Epson',   'WorkForce Pro WF-C5790',  'inyeccion', true,  true,  self::CONSUMIBLES_INYECCION_COLOR],
        ['Canon',   'PIXMA G3110',             'inyeccion', true,  true,  self::CONSUMIBLES_INYECCION_COLOR],
        ['Canon',   'imageCLASS MF453dw',      'laser',     false, true,  self::CONSUMIBLES_LASER_MONO],
        ['Brother', 'DCP-L2540DW',             'laser',     false, true,  self::CONSUMIBLES_LASER_MONO],
        ['Brother', 'HL-L8360CDW',             'laser',     true,  false, self::CONSUMIBLES_LASER_COLOR],
        ['Ricoh',   'SP 3710DN',               'laser',     false, false, self::CONSUMIBLES_LASER_MONO],
    ];

    public function run(): void
    {
        foreach (self::MODELOS as [$marca, $modelo, $tipo, $color, $mfp, $consumibles]) {
            ImpresoraModelo::firstOrCreate(
                ['marca' => $marca, 'modelo' => $modelo],
                [
                    'tipo_tecnologia'       => $tipo,
                    'color'                 => $color,
                    'es_multifuncional'     => $mfp,
                    'consumibles_sugeridos' => $consumibles,
                    'activo'                => true,
                ],
            );
        }

        $this->command->info('✔  10 modelos de impresora sembrados.');
    }
}