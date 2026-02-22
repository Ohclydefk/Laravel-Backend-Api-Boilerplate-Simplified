<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use App\Traits\DataValidator\DataValidatorTrait;
use App\Validations\UserValidation;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    use DataValidatorTrait;

    public function __construct(
        protected UserService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        return $this->service->list($request);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateData(
            $request,
            UserValidation::store()
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->create($validated)
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $this->validateData(
            $request,
            UserValidation::update($id)
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->update($id, $validated)
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
