<?php

namespace Modules\Inventory\Models\Expense;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Expense\ExpenseFilter;
use Modules\Core\Models\Warehouse\Warehouse;
use Modules\Core\Models\User\User;
use Modules\Inventory\Models\ExpenseCategory\ExpenseCategory;

class Expense extends TenantBaseModel
{
    use HasFactory, SoftDeletes, Searchable;
    use Searchable {
        Searchable::search as parentSearch;
    }

    protected $fillable = [
        'date',
        'Ref',
        'details',
        'amount',
        'expense_category_id',
        'warehouse_id',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->Ref)) {
                $model->Ref = self::getNumberOrder();
            }
        });
    }

    public static function getNumberOrder()
    {
        $last = static::withTrashed()->latest('id')->first();

        if ($last && !empty($last->Ref)) {
            $parts = explode('_', $last->Ref);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber = $lastNumber + 1;
            $code = 'EXP_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $code = 'EXP_0001';
        }

        while (self::where('Ref', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber = $lastNumber + 1;
            $code = 'EXP_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function toSearchableArray(): array
    {
        return [
            'Ref'     => $this->Ref,
            'details' => $this->details,
            'date'    => $this->date,
        ];
    }

    public function scopeFilter($query, ExpenseFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('expenses.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if ($order_by == 'Ref') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'date') {
            return $this->orderBy($order_by, $order_type);
        }

        return $this->orderBy('created_at', 'desc');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
