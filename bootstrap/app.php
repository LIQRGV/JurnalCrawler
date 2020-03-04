<?php

use LIQRGV\JurnalCrawler\CrawlerConsole;

defined('LUMEN_START') or define('LUMEN_START', microtime(time()));

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();

$app->withEloquent();

/**
 * -Load Config from config dir
 */
$configPath = $app->basePath('config');

$glob = glob($configPath . '/*.php');

if ($glob !== false) {
// To get the appropriate files, we'll simply glob the directory and filter
// out any "files" that are not truly files so we do not end up with any
// directories in our list, but only true files within the directory.
    $configPathLength = strlen($configPath);
    array_walk($glob, function ($file) use ($app, $configPathLength) {
        if (filetype($file) == 'file') {
            $configName = substr(substr($file, 1, -(5 - strlen($file))), $configPathLength);
            $app->configure($configName);

        }
    });
}
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

// Register provider that listed in config/app.php
$providers = $app->make('config')->get('app.providers');
if (!empty($providers)) {
    array_walk($providers, function ($provider) use ($app) {
        $app->register($provider);
    });
}

// Add model factory
$app->afterResolving(Illuminate\Database\Eloquent\Factory::class, function ($factory) {
    $factory->load(__DIR__ . '/../resources/database/Factories');
});


app('translator')->setLocale('en');


$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    CrawlerConsole::class
);


$app->instance(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    new Laravel\Lumen\Exceptions\Handler()
);


/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);


/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'LIQRGV\JurnalCrawler\Http\Controllers',
    'prefix' => 'api',
], function ($router) {
    require __DIR__.'/../routes/api.php';
});

return $app;
