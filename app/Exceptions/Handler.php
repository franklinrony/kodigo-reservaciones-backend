<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions
            logger()->error('Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions and return standardized JSON responses.
     *
     * @param \Throwable $exception
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException(Throwable $exception, $request)
    {
        $status = 500;
        $data = [
            'message' => 'Error del servidor',
            'errors' => [],
        ];

        if ($exception instanceof ValidationException) {
            $status = 422;
            $data['message'] = 'Error de validación';
            $data['errors'] = $exception->errors();
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $data['message'] = 'No autorizado para realizar esta acción';
        } elseif ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $data['message'] = 'El recurso solicitado no existe';
        } elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $data['message'] = 'Endpoint no encontrado';
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $data['message'] = $exception->getMessage() ?: 'Error HTTP';
        } else {
            if (config('app.debug')) {
                $data['debug'] = [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => explode("\n", $exception->getTraceAsString()),
                ];
            }
        }

        return response()->json($data, $status);
    }
}