<?php

namespace Modules\Core\Http\Controllers\City;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\City\StoreRequest;
use Modules\Core\Http\Requests\City\UpdateRequest;
use Modules\Core\Models\City\City;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Filters\City\CityFilter;

class CityController extends Controller
{
    protected $city;

    public function __construct(CityInterface $city)
    {
        $this->city = $city;
        
        $this->middleware('permission:read-city',  ['only' => ['index']]);
        $this->middleware('permission:show-city',  ['only' => ['show']]);
        $this->middleware('permission:create-city', ['only' => ['store']]);
        $this->middleware('permission:update-city', ['only' => ['update']]);
        $this->middleware('permission:delete-city', ['only' => ['destroy']]);
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
    public function show(City $city)
    {
        return $this->city->show($city);
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
    public function update(City $city, UpdateRequest $request)
    {
        return $this->city->update($city, $request);
    }


    
    /**
     * Remove the specified resource from storage.
    */
    public function destroy(City $city)
    {
        return $this->city->destroy($city);
    }
}
