<?php

namespace Modules\Inventory\Models\PurchaseReturnDetail;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Models\PurchaseReturn\PurchaseReturn;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;
use Modules\Inventory\Models\Unit\Unit;

class PurchaseReturnDetail extends TenantBaseModel
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'product_variant_id',
        'cost',
        'purchase_unit_id',
        'TaxNet',
        'tax_method',
        'discount',
        'discount_method',
        'quantity',
        'total',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id', 'id');
    }
}
