<?php

use Illuminate\Support\Str;

return [

/*
    |--------------------------------------------------------------------------
    | Controlador de Sesión Predeterminado
    |--------------------------------------------------------------------------
    |
    | Esta opción determina el controlador de sesión predeterminado que se utiliza para
    | las solicitudes entrantes. Laravel soporta una variedad de opciones de almacenamiento para
    | persistir datos de sesión. El almacenamiento en base de datos es una gran opción predeterminada.
    |
    | Soportado: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

/*
    |--------------------------------------------------------------------------
    | Duración de la Sesión
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar el número de minutos que deseas que la sesión
    | pueda permanecer inactiva antes de que expire. Si quieres que expiren inmediatamente
    | cuando el navegador se cierre, puedes indicarlo mediante la opción de configuración expire_on_close.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

/*
    |--------------------------------------------------------------------------
    | Cifrado de Sesión
    |--------------------------------------------------------------------------
    |
    | Esta opción te permite especificar fácilmente que todos tus datos de sesión
    | deben cifrarse antes de almacenarse. Todo el cifrado se realiza
    | automáticamente por Laravel y puedes usar la sesión como normal.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

/*
    |--------------------------------------------------------------------------
    | Ubicación del Archivo de Sesión
    |--------------------------------------------------------------------------
    |
    | Al utilizar el controlador de sesión "file", los archivos de sesión se colocan
    | en disco. La ubicación de almacenamiento predeterminada está definida aquí; sin embargo, eres
    | libre de proporcionar otra ubicación donde deben almacenarse.
    |
    */

    'files' => storage_path('framework/sessions'),

/*
    |--------------------------------------------------------------------------
    | Conexión de Base de Datos de Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar los controladores de sesión "database" o "redis", puedes especificar una
    | conexión que debe usarse para gestionar estas sesiones. Esto debe
    | corresponder a una conexión en tus opciones de configuración de base de datos.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

/*
    |--------------------------------------------------------------------------
    | Tabla de Base de Datos de Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar el controlador de sesión "database", puedes especificar la tabla a
    | utilizar para almacenar las sesiones. Por supuesto, se define un predeterminado sensato
    | para ti; sin embargo, puedes cambiarlo por otra tabla.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

/*
    |--------------------------------------------------------------------------
    | Almacén de Caché de Sesión
    |--------------------------------------------------------------------------
    |
    | Al usar uno de los backends de sesión impulsados por caché del framework, puedes
    | definir el almacén de caché que debe usarse para almacenar los datos de sesión
    | entre solicitudes. Esto debe coincidir con uno de tus almacenes de caché definidos.
    |
    | Afecta: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

/*
    |--------------------------------------------------------------------------
    | Lotería de Limpieza de Sesiones
    |--------------------------------------------------------------------------
    |
    | Algunos controladores de sesión deben limpiar manualmente su ubicación de almacenamiento para eliminar
    | sesiones antiguas del almacenamiento. Aquí están las probabilidades de que ocurra
    | en una solicitud dada. Por defecto, las probabilidades son 2 de 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the session cookie that is created by
    | the framework. Typically, you should not need to change this value
    | since doing so does not grant a meaningful security improvement.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application, but you're free to change this when necessary.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | This value determines the domain and subdomains the session cookie is
    | available to. By default, the cookie will be available to the root
    | domain without subdomains. Typically, this shouldn't be changed.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you when it can't be done securely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. It's unlikely you should disable this option.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" to permit secure cross-site requests.
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will tie the cookie to the top-level site for
    | a cross-site context. Partitioned cookies are accepted by the browser
    | when flagged "secure" and the Same-Site attribute is set to "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
