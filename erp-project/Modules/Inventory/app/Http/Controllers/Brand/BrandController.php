<?php

namespace Modules\Inventory\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Brand\StoreRequest;
use Modules\Inventory\Http\Requests\Brand\UpdateRequest;
use Modules\Inventory\Http\Requests\Brand\ChangeStatusRequest;
use Modules\Inventory\Repositories\Brand\BrandInterface;
use Modules\Inventory\Filters\Brand\BrandFilter;

class BrandController extends Controller
{
    protected $brand;

    public function __construct(BrandInterface $brand)
    {
        $this->brand = $brand;

        $this->middleware('permission:read-brand,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-brand,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-brand,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-brand,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-brand,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-brand,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, BrandFilter $filter)
    {
        return $this->brand->index($request, $filter);
    }

    public function show($id)
    {
        return $this->brand->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->brand->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->brand->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->brand->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->brand->changeStatus($id, $request);
    }
}
