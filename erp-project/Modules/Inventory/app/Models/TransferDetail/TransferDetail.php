<?php

namespace Modules\Inventory\Models\TransferDetail;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Models\Transfer\Transfer;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\ProductVariant\ProductVariant;
use Modules\Inventory\Models\Unit\Unit;

class TransferDetail extends TenantBaseModel
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'product_variant_id',
        'cost',
        'TaxNet',
        'tax_method',
        'discount',
        'discount_method',
        'quantity',
        'purchase_unit_id',
        'total',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id', 'id');
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
