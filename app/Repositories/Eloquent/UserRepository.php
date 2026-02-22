<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\BaseQuery\BaseQueryTrait;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    use BaseQueryTrait;

    protected array $searchable = [
        'name',
        'email'
    ];

    protected array $sortable = [
        'name',
        'email',
        'created_at'
    ];

    public function paginate(Request $request)
    {
        $query = User::query();

        return $this->applyQueryFeatures(
            $query,
            $request,
            $this->searchable,
            $this->sortable
        );
    }

    public function find(int $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id)
    {
        $user = $this->find($id);
        $user->delete();
        return true;
    }
}
