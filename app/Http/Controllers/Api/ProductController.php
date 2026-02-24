<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ProductService;
use App\Validations\ProductValidation;
use App\Http\Controllers\BaseController;

class ProductController extends BaseController
{

    protected ProductService $service;

    public function __construct(ProductService $service)
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
            'Product retrieved successfully.',
            $service->find($id)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $service = $this->service;

        $validated = $this->validateData(
            $request,
            ProductValidation::store()
        );

        return $this->success(
            'Product created successfully.',
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
            ProductValidation::update($id)
        );

        return $this->success(
            'Product updated successfully.',
            $service->update($id, $validated)
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $service = $this->service;

        $service->delete($id);

        return $this->success(
            'Product deleted successfully.'
        );
    }
}
