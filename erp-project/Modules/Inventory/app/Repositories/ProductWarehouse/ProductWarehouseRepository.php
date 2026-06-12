<?php

namespace Modules\Inventory\Repositories\ProductWarehouse;

use Modules\Inventory\Models\ProductWarehouse\ProductWarehouse;

class ProductWarehouseRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new ProductWarehouse();
    }
}
