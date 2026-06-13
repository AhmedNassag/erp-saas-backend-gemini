<?php

namespace Modules\Inventory\Repositories\SaleDetail;

use Modules\Inventory\Models\SaleDetail\SaleDetail;

class SaleDetailRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new SaleDetail();
    }
}
