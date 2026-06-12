<?php

namespace Modules\Inventory\Repositories\ProductVariant;

use Modules\Inventory\Models\ProductVariant\ProductVariant;

class ProductVariantRepository
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new ProductVariant();
    }
}
