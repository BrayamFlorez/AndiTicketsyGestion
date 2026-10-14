<?php

/*
|--------------------------------------------------------------------------
| Agente de inventario de Windows
|--------------------------------------------------------------------------
*/

return [

    // Cada cuánto reporta el agente. Se envía en cada respuesta, así que
    // cambiarlo aquí aplica a todos los agentes sin reinstalar.
    'intervalo_minutos' => (int) env('AGENTE_INTERVALO_MINUTOS', 60),

    // Minutos sin reportar para considerar un agente "sin conexión"
    'minutos_sin_conexion' => (int) env('AGENTE_MINUTOS_SIN_CONEXION', 180),

    // Categorías de Equipo::CATEGORIAS que el instalador NO ofrece (no son PCs con Windows)
    'categorias_excluidas' => ['impresora'],

    // Tipo de chasis que detecta el agente → categoría sugerida en el instalador.
    // Si la categoría no existe en Equipo::CATEGORIAS se sugiere la primera disponible.
    'categoria_por_chasis' => [
        'escritorio'  => 'computadora',
        'portatil'    => 'portatil',
        'todo_en_uno' => 'aio',
    ],

    // Usuario que queda como "creado por" en los equipos que crea el agente.
    // Obligatorio si la columna equipos.creado_por no admite NULL.
    'usuario_creador_id' => env('AGENTE_USUARIO_CREADOR_ID'),

    // Intentos de instalación por minuto y por IP (frena pruebas con llaves inventadas)
    'intentos_registro_por_minuto' => (int) env('AGENTE_INTENTOS_REGISTRO', 30),

    /*
    | Dato que envía el agente → posibles nombres del campo en Equipo::ESPECIFICACIONES_SCHEMA.
    | Solo se llenan los campos que existan en el esquema de la categoría del equipo.
    */
    'alias_especificaciones' => [
        'procesador' => ['procesador', 'cpu'],
        'sistema'    => ['sistema', 'sistema_operativo', 'so'],
        'ram'        => ['ram', 'ram_gb', 'memoria_ram'],
        'disco'      => ['disco', 'almacenamiento', 'almacenamiento_gb', 'disco_gb'],
        'tipo_disco' => ['tipo_disco', 'tipo_almacenamiento'],
    ],

    // Seriales genéricos de fabricante que no identifican al equipo
    'seriales_invalidos' => [
        'to be filled by o.e.m.', 'default string', 'system serial number',
        'not specified', 'none', 'n/a', '0', '00000000', '0123456789', 'chassis serial number',
    ],
];
