<?php

namespace Modules\Inventory\Models\ExpenseCategory;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\ExpenseCategory\ExpenseCategoryFilter;

class ExpenseCategory extends TenantBaseModel
{
    use HasFactory, SoftDeletes, Searchable;
    use Searchable {
        Searchable::search as parentSearch;
    }

    protected $fillable = [
        'name',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }

    public function toSearchableArray(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }

    public function scopeFilter($query, ExpenseCategoryFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('expense_categories.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if ($order_by == 'name') {
            return $this->orderBy($order_by, $order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }
}
