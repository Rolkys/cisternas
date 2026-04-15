<?php

use Illuminate\Support\Str;

return [

/*
    |--------------------------------------------------------------------------
    | Nombre de Conexión de Base de Datos Predeterminada
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar cuál de las conexiones de base de datos inferiores deseas
    | usar como conexión predeterminada para todo el trabajo de base de datos. Por supuesto
    | puedes usar muchas conexiones a la vez usando la biblioteca de Base de Datos.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlsrv'),

/*
    |--------------------------------------------------------------------------
    | Conexiones de Base de Datos
    |--------------------------------------------------------------------------
    |
    | Aquí están todas las conexiones de base de datos configuradas para tu aplicación.
    | Por supuesto, ejemplos de configuración de cada plataforma de base de datos soportada
    | por Laravel se muestran abajo para hacer el desarrollo simple.
    |
    |
    | Todo el trabajo de base de datos en Laravel se hace a través de las instalaciones PHP PDO
    | así que asegúrate de tener el controlador para tu base de datos en particular
    | instalado en tu máquina antes de comenzar el desarrollo.
    |
    */ 

    'connections' => [

        'sqlsrv' => [
                'driver' => 'sqlsrv',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '192.168.1.253'),
                'port' => env('DB_PORT', '1433'),
                'database' => env('DB_DATABASE', 'CISTERNAS'),
                'username' => env('DB_USERNAME', 'cisternas'),
                'password' => env('DB_PASSWORD', 'Cisternas2026$*'),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'options' => [
                    'ConnectTimeout' => 30,
                ],
            ],

    ],

/*
    |--------------------------------------------------------------------------
    | Tabla del Repositorio de Migraciones
    |--------------------------------------------------------------------------
    |
    | Esta tabla lleva el registro de todas las migraciones que ya se han ejecutado para
    | tu aplicación. Usando esta información, podemos determinar cuáles de
    | las migraciones en disco no se han ejecutado realmente en la base de datos.
    |
    */ 

'migraciones' => 'migraciones',

/*
    |--------------------------------------------------------------------------
    | Bases de Datos Redis
    |--------------------------------------------------------------------------
    |
    | Redis es una tienda de clave-valor de código abierto, rápida y avanzada que también
    | proporciona un conjunto de comandos más rico que un sistema clave-valor típico
    | como APC o Memcached. Laravel hace que sea fácil empezar.
    |
    */ 

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
