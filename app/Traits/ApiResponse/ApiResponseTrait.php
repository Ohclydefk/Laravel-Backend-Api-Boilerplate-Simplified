<?php

namespace App\Traits\ApiResponse;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function success(
        string $message = 'Success',
        $data = null,
        $meta = null,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null
        ], $code);
    }

    protected function error(
        string $message = 'Error',
        $errors = null,
        int $code = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors
        ], $code);
    }
}
