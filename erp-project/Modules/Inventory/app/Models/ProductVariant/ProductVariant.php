<?php

namespace Modules\Inventory\Models\ProductVariant;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Inventory\Models\Product\Product;

class ProductVariant extends TenantBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
