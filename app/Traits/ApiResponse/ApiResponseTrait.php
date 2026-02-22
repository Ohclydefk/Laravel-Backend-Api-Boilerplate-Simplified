<?php

namespace App\Traits\ApiResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait ApiResponseTrait
{
    protected function success(
        string $message = 'Success',
        $data = null,
        $meta = null,
        int $code = 200
    ): JsonResponse {

        // Handle empty array or empty collection
        if (
            (is_array($data) && empty($data)) ||
            ($data instanceof Collection && $data->isEmpty())
        ) {
            return response()->json([
                'success' => true,
                'message' => 'No records found.',
                'data' => [],
                'meta' => $meta,
                'errors' => null
            ], 200);
        }

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

    protected function notFound(
        string $message = 'Resource not found.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => null
        ], 404);
    }
}