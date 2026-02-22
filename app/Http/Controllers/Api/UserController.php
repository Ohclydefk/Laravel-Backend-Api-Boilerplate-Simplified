<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use App\Traits\DataValidator\DataValidatorTrait;
use App\Validations\UserValidation;
use App\Http\Controllers\BaseController;

class UserController extends BaseController
{
    use DataValidatorTrait;

    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $service = $this->service;

        return $service->list($request);
    }

    public function show(int $id): JsonResponse
    {
        $service = $this->service;

        return $this->success(
            'User retrieved successfully.',
            $service->find($id)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $service = $this->service;

        $validated = $this->validateData(
            $request,
            UserValidation::store()
        );

        return $this->success(
            'User created successfully.',
            $service->create($validated),
            null,
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $service = $this->service;

        $validated = $this->validateData(
            $request,
            UserValidation::update($id)
        );

        return $this->success(
            'User updated successfully.',
            $service->update($id, $validated)
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $service = $this->service;

        $service->delete($id);

        return $this->success(
            'User deleted successfully.'
        );
    }
}
