<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // VALIDATION ERROR
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $exception->errors()
            ], 422);
        }

        // DATA NOT FOUND
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // ROUTE NOT FOUND
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint tidak ditemukan'
            ], 404);
        }

        // METHOD SALAH
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ], 405);
        }

        // ERROR UMUM
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage()
        ], 500);
    }
}
