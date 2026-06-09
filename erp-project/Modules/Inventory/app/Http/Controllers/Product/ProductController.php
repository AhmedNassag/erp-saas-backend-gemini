<?php

namespace Modules\Inventory\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Product\StoreRequest;
use Modules\Inventory\Http\Requests\Product\UpdateRequest;
use Modules\Inventory\Http\Requests\Product\ChangeStatusRequest;
use Modules\Inventory\Repositories\Product\ProductInterface;
use Modules\Inventory\Filters\Product\ProductFilter;

class ProductController extends Controller
{
    protected $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;

        $this->middleware('permission:read-product,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-product,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-product,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-product,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-product,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-product,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, ProductFilter $filter)
    {
        return $this->product->index($request, $filter);
    }

    public function show($id)
    {
        return $this->product->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->product->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->product->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->product->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->product->changeStatus($id, $request);
    }
}
