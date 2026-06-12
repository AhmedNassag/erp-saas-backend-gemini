<?php

namespace Modules\Inventory\Http\Controllers\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Adjustment\StoreRequest;
use Modules\Inventory\Http\Requests\Adjustment\UpdateRequest;
use Modules\Inventory\Repositories\Adjustment\AdjustmentInterface;
use Modules\Inventory\Filters\Adjustment\AdjustmentFilter;

class AdjustmentController extends Controller
{
    protected $adjustment;

    public function __construct(AdjustmentInterface $adjustment)
    {
        $this->adjustment = $adjustment;

        $this->middleware('permission:read-adjustment,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-adjustment,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-adjustment,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-adjustment,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-adjustment,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, AdjustmentFilter $filter)
    {
        return $this->adjustment->index($request, $filter);
    }

    public function show($id)
    {
        return $this->adjustment->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->adjustment->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->adjustment->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->adjustment->destroy($id);
    }
}
