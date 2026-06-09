<?php

namespace Modules\Inventory\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Unit\StoreRequest;
use Modules\Inventory\Http\Requests\Unit\UpdateRequest;
use Modules\Inventory\Http\Requests\Unit\ChangeStatusRequest;
use Modules\Inventory\Repositories\Unit\UnitInterface;
use Modules\Inventory\Filters\Unit\UnitFilter;

class UnitController extends Controller
{
    protected $unit;

    public function __construct(UnitInterface $unit)
    {
        $this->unit = $unit;

        $this->middleware('permission:read-unit,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-unit,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-unit,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-unit,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-unit,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-unit,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, UnitFilter $filter)
    {
        return $this->unit->index($request, $filter);
    }

    public function show($id)
    {
        return $this->unit->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->unit->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->unit->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->unit->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->unit->changeStatus($id, $request);
    }
}
