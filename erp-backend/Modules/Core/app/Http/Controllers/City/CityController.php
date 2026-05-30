<?php

namespace Modules\Core\Http\Controllers\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\City\StoreRequest;
use Modules\Core\Http\Requests\City\UpdateRequest;
use Modules\Core\Http\Requests\City\ChangeStatusRequest;
use Modules\Core\Models\City\City;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Filters\City\CityFilter;

class CityController extends Controller
{
    protected $city;

    public function __construct(CityInterface $city)
    {
        $this->city = $city;
        
        $this->middleware('permission:read-city,tenant',  ['only' => ['index']]);
        $this->middleware('permission:show-city,tenant',  ['only' => ['show']]);
        $this->middleware('permission:create-city,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-city,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-city,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-city,tenant', ['only' => ['changeStatus']]);
    }



    /**
     * Display a listing of the resource.
    */
    public function index(Request $request, CityFilter $filter)
    {
        return $this->city->index($request, $filter);
    }



    /**
     * Show the specified resource.
    */
    public function show($id)
    {
        return $this->city->show($id);
    }



    /**
     * Show the form for creating a new resource.
    */
    public function store(StoreRequest $request)
    {
        return $this->city->store($request);
    }



    /**
     * Show the form for editing the specified resource.
    */
    public function update($id, UpdateRequest $request)
    {
        return $this->city->update($id, $request);
    }


    
    /**
     * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        return $this->city->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->city->changeStatus($id, $request);
    }
}
