<?php

namespace Modules\Core\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\RoleAndPermission\Role\StoreRequest;
use Modules\Core\Http\Requests\RoleAndPermission\Role\UpdateRequest;
use Modules\Core\Http\Requests\RoleAndPermission\Role\ChangeStatusRequest;
use Modules\Core\Models\RoleAndPermission\Role;
use Modules\Core\Repositories\RoleAndPermission\RoleInterface;

class RoleController extends Controller
{
    protected $role;

    public function __construct(RoleInterface $role)
    {
        $this->role = $role;

        $this->middleware('permission:read-role,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-role,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-role,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-role,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-role,tenant', ['only' => ['destroy']]);
    }



    /**
     * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        return $this->role->index($request);
    }



    /**
     * Show the specified resource.
    */
    public function show($id)
    {
        return $this->role->show($id);
    }



    /**
     * Store a newly created resource in storage.
    */
    public function store(StoreRequest $request)
    {
        return $this->role->store($request);
    }



    /**
     * Update the specified resource in storage.
    */
    public function update($id , UpdateRequest $request)
    {
        return $this->role->update($id, $request);
    }



    /**
     * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        return $this->role->destroy($id);
    }
}
