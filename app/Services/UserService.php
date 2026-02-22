<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function list(Request $request)
    {
        return $this->repository->paginate($request);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

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
