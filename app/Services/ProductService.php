<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function list(Request $request)
    {
        return $this->repository->index($request);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
