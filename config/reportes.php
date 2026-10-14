<?php

/*
|--------------------------------------------------------------------------
| Datos de la empresa en los reportes PDF
|--------------------------------------------------------------------------
| El logo es una ruta dentro de public/ (PNG o JPG). Si no existe, el reporte sale solo con el nombre.
*/

return [

    'empresa' => [
        'nombre'    => env('REPORTE_EMPRESA_NOMBRE', env('APP_NAME', 'Mi empresa')),
        'nit'       => env('REPORTE_EMPRESA_NIT'),
        'telefono'  => env('REPORTE_EMPRESA_TELEFONO'),
        'correo'    => env('REPORTE_EMPRESA_CORREO'),
        'direccion' => env('REPORTE_EMPRESA_DIRECCION'),
        'ciudad'    => env('REPORTE_EMPRESA_CIUDAD'),
        'sitio_web' => env('REPORTE_EMPRESA_SITIO_WEB'),
        'logo'      => env('REPORTE_EMPRESA_LOGO', 'images/logo-reportes.png'),
    ],

    // Reporte de servicio técnico a equipos ("Contrato de prestación de servicio")
    'equipos' => [
        'titulo'        => env('REPORTE_EQUIPOS_TITULO', 'CONTRATO DE PRESTACIÓN DE SERVICIO'),
        'subtitulo'     => env('REPORTE_EQUIPOS_SUBTITULO', 'DEPARTAMENTO TÉCNICO'),
        'prefijo_orden' => env('REPORTE_EQUIPOS_PREFIJO_ORDEN', 'MT-'),
        'clausula'      => env('REPORTE_EQUIPOS_CLAUSULA', 'Después de 30 días no se responde por los equipos.'),
    ],

];
