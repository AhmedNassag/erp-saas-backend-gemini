<?php

namespace Modules\Core\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\Branch\StoreRequest;
use Modules\Core\Http\Requests\Branch\UpdateRequest;
use Modules\Core\Http\Requests\Branch\ChangeStatusRequest;
use Modules\Core\Repositories\Branch\BranchInterface;
use Modules\Core\Filters\Branch\BranchFilter;
use Modules\Core\Models\Branch\Branch;

class BranchController extends Controller
{
    protected $branch;

    public function __construct(BranchInterface $branch)
    {
        $this->branch = $branch;
        
        $this->middleware('permission:read-branch,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-branch,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-branch,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-branch,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-branch,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-branch,tenant', ['only' => ['changeStatus']]);
    }


    
    public function index(Request $request, BranchFilter $filter)
    {
        return $this->branch->index($request, $filter);
    }



    public function show($id)
    {
        return $this->branch->show($id);
    }



    public function store(StoreRequest $request)
    {
        return $this->branch->store($request);
    }



    public function update($id, UpdateRequest $request)
    {
        return $this->branch->update($id, $request);
    }


    
    public function destroy($id)
    {
        return $this->branch->destroy($id);
    }



    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->branch->changeStatus($id, $request);
    }
}
