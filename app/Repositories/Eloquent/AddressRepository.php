<?php

namespace App\Repositories\Eloquent;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Traits\BaseQuery\BaseQueryTrait;
use App\Repositories\Contracts\AddressRepositoryInterface;

class AddressRepository implements AddressRepositoryInterface
{
    use BaseQueryTrait;

    protected array $searchable = [
        'label',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    protected array $sortable = [
        'label',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    public function index(Request $request)
    {
        return $this->paginateList($request, new Address, $this->searchable, $this->sortable);
    }

    public function find(int $id)
    {
        return Address::findOrFail($id);
    }

    public function create(array $data)
    {
        return Address::create($data);
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
