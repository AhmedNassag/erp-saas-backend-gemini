<?php

namespace Modules\Inventory\Repositories\PurchaseReturnDetail;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\PurchaseReturnDetail\PurchaseReturnDetail;

class PurchaseReturnDetailRepository extends BaseRepository
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PurchaseReturnDetail();
    }

    protected function getResourceClass(): string
    {
        return '';
    }

    protected function getPluralName(): string
    {
        return 'Purchase Return Details';
    }

    protected function getSingularName(): string
    {
        return 'Purchase Return Detail';
    }
}
