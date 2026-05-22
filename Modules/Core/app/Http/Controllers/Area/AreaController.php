<?php

namespace Modules\Core\App\Http\Controllers\Area;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\App\Http\Requests\StoreRequest;
use Modules\Core\App\Http\Requests\UpdateRequest;
use Modules\Core\App\Models\Area;
use Modules\Core\App\Repositories\AreaInterface;
use Modules\Core\App\Filters\AreaFilter;

class AreaController extends Controller
{
    protected $area;

    public function __construct(AreaInterface $area)
    {
        $this->area = $area;
    }



    /** Display a listing of the resource **/
    public function index(Request $request, AreaFilter $filter)
    {
        return $this->area->index($request, $filter);
    }



    /** Show the form for creating a new resource **/
    public function store(StoreRequest $request)
    {
        return $this->area->store($request);
    }



    /** Show the specified resource **/
    public function show(Area $area)
    {
        return $this->area->show($area);
    }



    /** Show the form for editing the specified resource **/
    public function update(Area $area, UpdateRequest $request)
    {
        return $this->area->update($area, $request);
    }



    /** Remove the specified resource from storage **/
    public function destroy(Area $area)
    {
        return $this->area->destroy($area);
    }
}
