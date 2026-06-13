<?php

namespace Modules\Inventory\Repositories\PurchaseDetail;

use Modules\Inventory\Models\PurchaseDetail\PurchaseDetail;

class PurchaseDetailRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PurchaseDetail();
    }
}
