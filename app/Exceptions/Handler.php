<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception): Response|JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $status = 500;
            $message = 'Server Error';

            if (method_exists($exception, 'getStatusCode')) {
                $status = $exception->getStatusCode();
            }
            if (method_exists($exception, 'getMessage') && $exception->getMessage()) {
                $message = $exception->getMessage();
            }

            return response()->json([
                'message' => $message,
            ], $status);
        }

        return parent::render($request, $exception);
    }


    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
