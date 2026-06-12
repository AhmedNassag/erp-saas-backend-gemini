<?php

namespace Modules\Inventory\Models\AdjustmentDetail;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Models\Adjustment\Adjustment;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;

class AdjustmentDetail extends TenantBaseModel
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'type',
        'adjustment_id',
        'product_id',
        'product_variant_id',
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }
}
