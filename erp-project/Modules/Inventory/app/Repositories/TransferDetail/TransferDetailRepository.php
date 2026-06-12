<?php

namespace Modules\Inventory\Repositories\TransferDetail;

use Modules\Inventory\Models\TransferDetail\TransferDetail;

class TransferDetailRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new TransferDetail();
    }
}
