<?php

namespace Modules\Inventory\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Expense\StoreRequest;
use Modules\Inventory\Http\Requests\Expense\UpdateRequest;
use Modules\Inventory\Repositories\Expense\ExpenseInterface;
use Modules\Inventory\Filters\Expense\ExpenseFilter;

class ExpenseController extends Controller
{
    protected $expense;

    public function __construct(ExpenseInterface $expense)
    {
        $this->expense = $expense;

        $this->middleware('permission:read-expense,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-expense,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-expense,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-expense,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-expense,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, ExpenseFilter $filter)
    {
        return $this->expense->index($request, $filter);
    }

    public function show($id)
    {
        return $this->expense->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->expense->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->expense->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->expense->destroy($id);
    }
}
