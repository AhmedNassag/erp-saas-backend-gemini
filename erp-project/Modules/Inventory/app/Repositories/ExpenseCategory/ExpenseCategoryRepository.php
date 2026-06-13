<?php

namespace Modules\Inventory\Repositories\ExpenseCategory;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\ExpenseCategory\ExpenseCategory;
use Modules\Inventory\Resources\ExpenseCategory\ExpenseCategoryResource;

class ExpenseCategoryRepository extends BaseRepository implements ExpenseCategoryInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new ExpenseCategory();
    }

    protected function getResourceClass(): string
    {
        return ExpenseCategoryResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Expense Categories';
    }

    protected function getSingularName(): string
    {
        return 'Expense Category';
    }
}
