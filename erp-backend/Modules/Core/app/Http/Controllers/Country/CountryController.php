<?php

namespace Modules\Core\Http\Controllers\Country;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Country\StoreRequest;
use Modules\Core\Http\Requests\Country\UpdateRequest;
use Modules\Core\Http\Requests\Country\ChangeStatusRequest;
use Modules\Core\Models\Country\Country;
use Modules\Core\Filters\Country\CountryFilter;
use Modules\Core\Repositories\Country\CountryInterface;

class CountryController extends Controller
{
    protected $country;

    public function __construct(CountryInterface $country)
    {
        $this->country = $country;
        
        $this->middleware('permission:read-country,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-country,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-country,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-country,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-country,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-country,tenant', ['only' => ['changeStatus']]);
    }



    /**
     * Display a listing of the resource.
    */
    public function index(Request $request, CountryFilter $filter)
    {
        return $this->country->index($request, $filter);
    }



    /**
     * Show the form for creating a new resource.
    */
    public function store(StoreRequest $request)
    {
        return $this->country->store($request);
    }



    /**
     * Show the specified resource.
    */
    public function show($id)
    {
        return $this->country->show($id);
    }



    /**
     * Show the form for editing the specified resource.
    */
    public function update($id, UpdateRequest $request)
    {
        return $this->country->update($id, $request);
    }


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->country->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->country->changeStatus($id, $request);
    }
}
