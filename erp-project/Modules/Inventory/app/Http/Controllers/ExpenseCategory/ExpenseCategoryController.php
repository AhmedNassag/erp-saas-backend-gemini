<?php

namespace Modules\Inventory\Http\Controllers\ExpenseCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\ExpenseCategory\StoreRequest;
use Modules\Inventory\Http\Requests\ExpenseCategory\UpdateRequest;
use Modules\Inventory\Http\Requests\ExpenseCategory\ChangeStatusRequest;
use Modules\Inventory\Repositories\ExpenseCategory\ExpenseCategoryInterface;
use Modules\Inventory\Filters\ExpenseCategory\ExpenseCategoryFilter;

class ExpenseCategoryController extends Controller
{
    protected $expenseCategory;

    public function __construct(ExpenseCategoryInterface $expenseCategory)
    {
        $this->expenseCategory = $expenseCategory;

        $this->middleware('permission:read-expense-category,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-expense-category,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-expense-category,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-expense-category,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-expense-category,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-expense-category,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, ExpenseCategoryFilter $filter)
    {
        return $this->expenseCategory->index($request, $filter);
    }

    public function show($id)
    {
        return $this->expenseCategory->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->expenseCategory->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->expenseCategory->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->expenseCategory->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->expenseCategory->changeStatus($id, $request);
    }
}
