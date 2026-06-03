<?php

namespace Modules\Core\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Area\StoreRequest;
use Modules\Core\Http\Requests\Area\UpdateRequest;
use Modules\Core\Http\Requests\Branch\ChangeStatusRequest;
use Modules\Core\Repositories\Area\AreaInterface;
use Modules\Core\Filters\Area\AreaFilter;
use Modules\Core\Models\Area\Area;

class AreaController extends Controller
{
    protected $area;

    public function __construct(AreaInterface $area)
    {
        $this->area = $area;

        $this->middleware('permission:read-area,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-area,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-area,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-area,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-area,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-area,tenant', ['only' => ['changeStatus']]);
    }

    

    public function index(Request $request, AreaFilter $filter)
    {
        return $this->area->index($request, $filter);
    }

    

    public function show($id)
    {
        return $this->area->show($id);
    }

    

    public function store(StoreRequest $request)
    {
        return $this->area->store($request);
    }



    public function update($id , UpdateRequest $request)
    {
        return $this->area->update($id , $request);
    }



    public function destroy($id)
    {
        return $this->area->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->area->changeStatus($id, $request);
    }
}
