<?php

namespace Modules\Inventory\Repositories\AdjustmentDetail;

use Modules\Inventory\Models\AdjustmentDetail\AdjustmentDetail;

class AdjustmentDetailRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new AdjustmentDetail();
    }
}
