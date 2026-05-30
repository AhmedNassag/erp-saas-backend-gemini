<?php

namespace Modules\Core\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Department\StoreRequest;
use Modules\Core\Http\Requests\Department\UpdateRequest;
use Modules\Core\Http\Requests\Department\ChangeStatusRequest;
use Modules\Core\Models\Department\Department;
use Modules\Core\Filters\Department\DepartmentFilter;
use Modules\Core\Repositories\Department\DepartmentInterface;

class DepartmentController extends Controller
{
    protected $department;

    public function __construct(DepartmentInterface $department)
    {
        $this->department = $department;
        
        $this->middleware('permission:read-department,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-department,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-department,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-department,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-department,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-department,tenant', ['only' => ['changeStatus']]);
    }



    /**
     * Display a listing of the resource.
    */
    public function index(Request $request, DepartmentFilter $filter)
    {
        return $this->department->index($request, $filter);
    }



    /**
     * Show the form for creating a new resource.
    */
    public function store(StoreRequest $request)
    {
        return $this->department->store($request);
    }



    /**
     * Show the specified resource.
    */
    public function show($id)
    {
        return $this->department->show($id);
    }



    /**
     * Show the form for editing the specified resource.
    */
    public function update($id, UpdateRequest $request)
    {
        return $this->department->update($id, $request);
    }


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->department->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->department->changeStatus($id, $request);
    }
}
