<?php

namespace Modules\Inventory\Repositories\Expense;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\Expense\Expense;
use Modules\Inventory\Resources\Expense\ExpenseResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseRepository extends BaseRepository implements ExpenseInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Expense();
    }

    protected function getResourceClass(): string
    {
        return ExpenseResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Expenses';
    }

    protected function getSingularName(): string
    {
        return 'Expense';
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data            = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $adjustment = $this->getModel()->create($data);

            DB::commit();
            
            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }
}
