<?php

namespace Modules\Inventory\Models\SaleDetail;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Models\Sale\Sale;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;
use Modules\Inventory\Models\Unit\Unit;

class SaleDetail extends TenantBaseModel
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_variant_id',
        'price',
        'sale_unit_id',
        'TaxNet',
        'tax_method',
        'discount',
        'discount_method',
        'quantity',
        'total',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
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
        return $this->belongsTo(Unit::class, 'sale_unit_id', 'id');
    }
}
