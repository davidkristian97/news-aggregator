<?php

use App\Exceptions\InvalidCredentialsException;
use App\Http\Responses\ErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (ValidationException $e) {
            $details = collect($e->errors())
                ->flatMap(fn ($messages, $field) => collect($messages)->map(fn ($message) => [
                    'field' => $field,
                    'issue' => $message,
                ]))
                ->values()
                ->all();

            return new ErrorResponse('The given data was invalid.', 422, $details);
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return new ErrorResponse('Resource not found.', 404);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return new ErrorResponse('Unauthenticated.', 401);
        });

        $exceptions->render(function (InvalidCredentialsException $e) {
            return new ErrorResponse($e->getMessage(), 401);
        });
    })->create();
