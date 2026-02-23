<?php

namespace App\Repositories\Eloquent;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Traits\BaseQuery\BaseQueryTrait;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
        'created_at',
    ];

    protected array $allowedRelationships = [
        'user'
    ];

    protected array $filterable = [
        'user_id',
        'city',
        'province',
        'country',
        'is_default',
    ];

    public function index(Request $request)
    {
        return $this->paginateList(
            $request,
            new Address,
            $this->searchable,
            $this->sortable,
            $this->allowedRelationships,
            $this->filterable,
            function ($query, $request) {

                // sample add computed column: full_address
                // customized Query Example: Concatenate address fields into a full_address column
                $query->addSelect([
                    'addresses.*',
                    DB::raw("
                    TRIM(CONCAT(
                        COALESCE(street, ''), ', ',
                        COALESCE(barangay, ''), ', ',
                        COALESCE(city, ''), ', ',
                        COALESCE(province, ''), ', ',
                        COALESCE(postal_code, ''), ', ',
                        COALESCE(country, '')
                    )) AS full_address
                ")
                ]);

                // example: custom range filter
                if ($request->filled('from')) {
                    $query->whereDate('created_at', '>=', $request->input('from'));
                }

                if ($request->filled('to')) {
                    $query->whereDate('created_at', '<=', $request->input('to'));
                }

                // example: constrain by related model
                if ($request->filled('user_status')) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('status', $request->input('user_status'));
                    });
                }
            }
        );
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
        $address = $this->find($id);
        $address->update($data);
        return $address;
    }

    public function delete(int $id)
    {
        $address = $this->find($id);
        $address->delete();
        return true;
    }
}
