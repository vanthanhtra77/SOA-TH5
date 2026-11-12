<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// Đăng ký các Client giao tiếp liên dịch vụ SOA
$app->singleton(
    App\Services\AuthServiceClient::class,
    fn () => new App\Services\AuthServiceClient()
);

$app->singleton(
    App\Services\ProductServiceClient::class,
    fn () => new App\Services\ProductServiceClient()
);

$app->singleton(
    App\Services\OrderServiceClient::class,
    fn () => new App\Services\OrderServiceClient()
);

$app->singleton(
    App\Services\ReportingService::class,
    fn ($app) => new App\Services\ReportingService(
        $app->make(App\Services\ProductServiceClient::class),
        $app->make(App\Services\OrderServiceClient::class)
    )
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
*/

$app->configure('app');
$app->configure('database');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
*/

$app->middleware([
    App\Http\Middleware\CorsMiddleware::class,
]);

$app->routeMiddleware([
    'auth.service' => App\Http\Middleware\AuthenticateViaAuthService::class,
]);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
