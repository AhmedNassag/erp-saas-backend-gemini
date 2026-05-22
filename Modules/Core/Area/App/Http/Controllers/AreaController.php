<?php

namespace Modules\Core\Area\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Area\App\Http\Requests\StoreRequest;
use Modules\Core\Area\App\Http\Requests\UpdateRequest;
use Modules\Core\Area\App\Models\Area;
use Modules\Core\Area\App\Repositories\AreaInterface;
use Modules\Core\Area\App\Filters\AreaFilter;
class AreaController extends Controller
{
    protected $area;

    public function __construct(AreaInterface $area)
    {
        $this->area = $area;

        $this->middleware('permission:read-area', ['only' => ['index']]);
        $this->middleware('permission:show-area', ['only' => ['show']]);
        $this->middleware('permission:create-area', ['only' => ['store']]);
        $this->middleware('permission:update-area', ['only' => ['update']]);
        $this->middleware('permission:delete-area', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request, AreaFilter $filter)
    {
        return $this->area->index($request,$filter);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(StoreRequest $request)
    {
        return $this->area->store($request);
    }

    /**
     * Show the specified resource.
     */
    public function show(Area $area)
    {
        return $this->area->show($area);
    }


    public function update(Area $area , UpdateRequest $request)
    {
        return $this->area->update($area , $request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        return $this->area->destroy($area);
    }
}
