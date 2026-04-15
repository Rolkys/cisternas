<?php

return [

/*
    |--------------------------------------------------------------------------
    | Nombre de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor es el nombre de tu aplicación. Este valor se utiliza cuando el
    | framework necesita colocar el nombre de la aplicación en una notificación o
    | cualquier otra ubicación requerida por la aplicación o sus paquetes.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

/*
    |--------------------------------------------------------------------------
    | Entorno de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor determina el \"entorno\" en el que tu aplicación está ejecutándose actualmente.
    | Esto puede determinar cómo prefieres configurar varios servicios que utiliza la aplicación.
    | Establece esto en tu archivo \".env\".
    |
    */

    'env' => env('APP_ENV', 'production'),

/*
    |--------------------------------------------------------------------------
    | Modo Depuración de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Cuando tu aplicación está en modo depuración, se mostrarán mensajes de error detallados con
    | rastros de pila en cada error que ocurra dentro de tu aplicación. Si está deshabilitado,
    | se muestra una página de error genérica simple.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

/*
    |--------------------------------------------------------------------------
    | URL de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Esta URL es utilizada por la consola para generar correctamente las URLs cuando se usa
    | la herramienta Artisan de línea de comandos. Deberías establecer esto en la raíz de
    | tu aplicación para que se use al ejecutar tareas Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

/*
    |--------------------------------------------------------------------------
    | Zona Horaria de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar la zona horaria predeterminada para tu aplicación, que
    | será utilizada por las funciones de fecha y fecha-hora de PHP. Hemos establecido
    | esto a un valor predeterminado sensato para ti de fábrica.
    |
    */

    'timezone' => 'UTC',

/*
    |--------------------------------------------------------------------------
    | Configuración de Idioma de la Aplicación
    |--------------------------------------------------------------------------
    |
    | El idioma de la aplicación determina el idioma predeterminado que se utilizará
    | por el proveedor de servicio de traducción. Eres libre de establecer este valor
    | a cualquiera de los idiomas soportados por la aplicación.
    |
    */

'locale' => 'es',

/*
    |--------------------------------------------------------------------------
    | Idioma de Respaldo de la Aplicación
    |--------------------------------------------------------------------------
    |
    | El idioma de respaldo determina el idioma a usar cuando el actual
    | no está disponible. Puedes cambiar el valor para que corresponda a cualquiera de
    | las carpetas de idioma proporcionadas por tu aplicación.
    |
    */

    'fallback_locale' => 'en',

/*
    |--------------------------------------------------------------------------
    | Idioma de Faker
    |--------------------------------------------------------------------------
    |
    | Este idioma será utilizado por la biblioteca Faker PHP al generar datos falsos
    | para tus semillas de base de datos. Por ejemplo, esto se usará para obtener
    | números de teléfono localizados, información de direcciones y más.
    |
    */

    'faker_locale' => 'en_US',

/*
    |--------------------------------------------------------------------------
    | Clave de Cifrado
    |--------------------------------------------------------------------------
    |
    | Esta clave es utilizada por el servicio de cifrado de Illuminate y debe establecerse
    | a una cadena aleatoria de 32 caracteres, de lo contrario estas cadenas cifradas
    | no serán seguras. ¡Por favor haz esto antes de desplegar una aplicación!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

/*
    |--------------------------------------------------------------------------
    | Proveedores de Servicios Autocargados
    |--------------------------------------------------------------------------
    |
    | Los proveedores de servicios listados aquí se cargarán automáticamente en la
    | solicitud a tu aplicación. Siéntete libre de añadir tus propios servicios a
    | este array para otorgar funcionalidad expandida a tus aplicaciones.
    |
    */

    'providers' => [

/*
         * Proveedores de Servicios del Framework Laravel...
         */ 
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

/*
         * Proveedores de Servicios de Paquetes...
         */ 

/*
         * Proveedores de Servicios de la Aplicación...
         */ 
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

/*
    |--------------------------------------------------------------------------
    | Alias de Clases
    |--------------------------------------------------------------------------
    |
    | Este array de alias de clases se registrará cuando esta aplicación
    | se inicie. Sin embargo, siéntete libre de registrar tantos como quieras ya
    | que los alias se cargan \"perezosamente\" por lo que no afectan el rendimiento.
    |
    */ 

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Js' => Illuminate\Support\Js::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

    ],

];
