<?php

namespace Modules\Core\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Warehouse\StoreRequest;
use Modules\Core\Http\Requests\Warehouse\UpdateRequest;
use Modules\Core\Http\Requests\Warehouse\ChangeStatusRequest;
use Modules\Core\Repositories\Warehouse\WarehouseInterface;
use Modules\Core\Filters\Warehouse\WarehouseFilter;

class WarehouseController extends Controller
{
    protected $warehouse;

    public function __construct(WarehouseInterface $warehouse)
    {
        $this->warehouse = $warehouse;

        $this->middleware('permission:read-warehouse,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-warehouse,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-warehouse,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-warehouse,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-warehouse,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-warehouse,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, WarehouseFilter $filter)
    {
        return $this->warehouse->index($request, $filter);
    }

    public function show($id)
    {
        return $this->warehouse->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->warehouse->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->warehouse->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->warehouse->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->warehouse->changeStatus($id, $request);
    }
}
