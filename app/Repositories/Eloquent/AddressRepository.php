<?php

namespace App\Repositories\Eloquent;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\AddressRepositoryInterface;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }

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
        $baseModel = $this->model; // use the base model
        $isSearchable = $this->searchable;
        $isSortable = $this->sortable;
        $relations = $this->allowedRelationships;
        $isFilterable = $this->filterable;

        return $this->paginateList(
            $request,
            $baseModel,
            $isSearchable,
            $isSortable,
            $relations,
            $isFilterable,
            function ($query, $request) {

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

                if ($request->filled('from')) {
                    $query->whereDate('created_at', '>=', $request->input('from'));
                }

                if ($request->filled('to')) {
                    $query->whereDate('created_at', '<=', $request->input('to'));
                }

                if ($request->filled('user_status')) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('status', $request->input('user_status'));
                    });
                }
            }
        );
    }
}
