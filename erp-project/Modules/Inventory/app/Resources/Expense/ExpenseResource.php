<?php

namespace Modules\Inventory\Resources\Expense;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'date'                   => $this->date,
            'Ref'                    => $this->Ref,
            'details'                => $this->details,
            'amount'                 => $this->amount,
            'expense_category_id'    => $this->expense_category_id,
            'expense_category_name'  => $this->expenseCategory?->name,
            'warehouse_id'           => $this->warehouse_id,
            'warehouse_name'         => $this->warehouse?->name,
            'user_id'                => $this->user_id,
            'user_name'              => $this->user?->name,
        ];
    }
}
