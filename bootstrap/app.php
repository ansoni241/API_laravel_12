<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        //PARA EL MANEJO DE EXCEPCIONES
        $exceptions->render(function (ValidationException $e, $request) {
            return ApiResponse::error('Error de validación', 422, $e->errors());
        });

        // $exceptions->render(function (Throwable $e, $request) {
        //     return ApiResponse::error($e->getMessage(), method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
        // });
    })->create();
