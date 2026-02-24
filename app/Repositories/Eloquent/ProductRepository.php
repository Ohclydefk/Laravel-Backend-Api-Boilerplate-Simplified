<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    protected array $searchable = [
        'name',
        'slug',
        'description',
        'sku',
        'price',
    ];

    protected array $sortable = [
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'stock',
    ];

    protected array $allowedRelationships = [];

    protected array $filterable = [
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'stock',
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
}
