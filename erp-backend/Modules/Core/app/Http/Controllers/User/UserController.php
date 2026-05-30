<?php

namespace Modules\Core\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\User\StoreRequest;
use Modules\Core\Http\Requests\User\UpdateRequest;
use Modules\Core\Http\Requests\User\ChangeStatusRequest;
use Modules\Core\Models\User\User;
use Modules\Core\Filters\User\UserFilter;
use Modules\Core\Repositories\User\UserInterface;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        
        $this->middleware('permission:read-user,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-user,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-user,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-user,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-user,tenant', ['only' => ['destroy']]);
        // $this->middleware('permission:changeStatus-user,tenant', ['only' => ['changeStatus']]);
        $this->middleware('permission:profile-user,tenant', ['only' => ['profile']]);
    }



    /**
     * Display a listing of the resource.
    */
    public function index(Request $request, UserFilter $filter)
    {
        return $this->user->index($request, $filter);
    }



    /**
     * Show the form for creating a new resource.
    */
    public function store(StoreRequest $request)
    {
        return $this->user->store($request);
    }



    /**
     * Show the specified resource.
    */
    public function show($id)
    {
        return $this->user->show($id);
    }



    /**
     * Show the form for editing the specified resource.
    */
    public function update($id, UpdateRequest $request)
    {
        return $this->user->update($id, $request);
    }



    
    /**
     * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        return $this->user->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->user->changeStatus($id, $request);
    }



    public function profile()
    {
        return $this->user->profile();
    }
}
