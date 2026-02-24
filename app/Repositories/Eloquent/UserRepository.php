<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    protected array $searchable = [
        'name',
        'email'
    ];

    protected array $sortable = [
        'name',
        'email',
        'created_at'
    ];

    protected array $allowedRelationships = [];

    protected array $filterable = [
        'name',
        'email'
    ];

    public function index(Request $request)
    {
        $baseModel = $this->model; // use the base model
        $isSearchable = $this->searchable;
        $isSortable = $this->sortable;
        $relations = $this->allowedRelationships;
        $isFilterable = $this->filterable;

        return $this->paginateList(
            $request,
            $baseModel,
            $isSearchable,
            $isSortable
        );
    }


    // Fully Customizable (override) the function from Base Repository
    public function update(int $id, array $data)
    {
        // Custom logic before calling parent
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::update($id, $data);
    }
}
