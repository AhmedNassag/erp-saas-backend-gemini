<?php

namespace Modules\Inventory\Resources\ExpenseCategory;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'status'      => $this->status ?? null,
            'description' => $this->description ?? null,
        ];
    }
}
