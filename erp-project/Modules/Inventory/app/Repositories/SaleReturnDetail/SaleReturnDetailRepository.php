<?php

namespace Modules\Inventory\Repositories\SaleReturnDetail;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\SaleReturnDetail\SaleReturnDetail;

class SaleReturnDetailRepository extends BaseRepository
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new SaleReturnDetail();
    }

    protected function getResourceClass(): string
    {
        return '';
    }

    protected function getPluralName(): string
    {
        return 'Sale Return Details';
    }

    protected function getSingularName(): string
    {
        return 'Sale Return Detail';
    }
}
