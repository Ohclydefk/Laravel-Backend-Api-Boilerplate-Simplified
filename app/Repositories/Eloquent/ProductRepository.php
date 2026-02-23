<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\BaseQuery\BaseQueryTrait;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    use BaseQueryTrait;

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

    public function index(Request $request)
    {
        return $this->paginateList($request, new Product, $this->searchable, $this->sortable);
    }

    public function find(int $id)
    {
        return Product::findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
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
