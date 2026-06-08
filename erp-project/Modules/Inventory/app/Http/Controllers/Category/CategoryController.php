<?php

namespace Modules\Inventory\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Category\StoreRequest;
use Modules\Inventory\Http\Requests\Category\UpdateRequest;
use Modules\Inventory\Http\Requests\Category\ChangeStatusRequest;
use Modules\Inventory\Repositories\Category\CategoryInterface;
use Modules\Inventory\Filters\Category\CategoryFilter;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;

        $this->middleware('permission:read-category,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-category,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-category,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-category,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-category,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-category,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, CategoryFilter $filter)
    {
        return $this->category->index($request, $filter);
    }

    public function show($id)
    {
        return $this->category->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->category->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->category->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->category->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->category->changeStatus($id, $request);
    }
}
