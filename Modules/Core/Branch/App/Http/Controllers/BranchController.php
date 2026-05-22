<?php

namespace Modules\Core\Branch\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Branch\App\Http\Requests\StoreRequest;
use Modules\Core\Branch\App\Http\Requests\UpdateRequest;
use Modules\Core\Branch\App\Models\Branch;
use Modules\Core\Branch\App\Repositories\BranchInterface;

class BranchController extends Controller
{
    protected $branch;

    public function __construct(BranchInterface $branch)
    {
        $this->branch = $branch;
        
        $this->middleware('permission:read-branch', ['only' => ['index']]);
        $this->middleware('permission:show-branch', ['only' => ['show']]);
        $this->middleware('permission:create-branch', ['only' => ['store']]);
        $this->middleware('permission:update-branch', ['only' => ['update']]);
        $this->middleware('permission:delete-branch', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        return $this->branch->index($request);
    }

    /**
     * Show the form for creating a new resource.
    */
    public function store(StoreRequest $request)
    {
        return $this->branch->store($request);
    }

    /**
     * Show the specified resource.
    */
    public function show(Branch $branch)
    {
        return $this->branch->show($branch);
    }

    /**
     * Show the form for editing the specified resource.
    */
    public function update(Branch $branch, UpdateRequest $request)
    {
        return $this->branch->update($branch, $request);
    }

    /**
     * Remove the specified resource from storage.
    */
    public function destroy(Branch $branch)
    {
        return $this->branch->destroy($branch);
    }
}
