<?php

namespace Modules\Inventory\Models\PurchaseDetail;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Models\Purchase\Purchase;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;
use Modules\Inventory\Models\Unit\Unit;

class PurchaseDetail extends TenantBaseModel
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
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

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
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
