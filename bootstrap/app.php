<?php

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Traits\ApiResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            // Create a simple class that uses the ApiResponseTrait
            $responseHandler = new class {
                use ApiResponseTrait;

                public function handle(Throwable $e)
                {
                    if ($e instanceof ValidationException) {
                        return $this->validationErrorResponse($e->errors());
                    }

                    if ($e instanceof ModelNotFoundException) {
                        return $this->notFoundResponse('Resource not found');
                    }

                    return null;
                }
            };

            return $responseHandler->handle($e) ?? response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        });
    })->create();
