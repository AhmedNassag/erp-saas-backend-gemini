<?php

namespace Modules\Inventory\Filters\Provider;

use App\Filters\Filters;

class ProviderFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'code',
        'phone',
        'area_id',
        'adresse',
    ];

    public function adresse($adresse)
    {
        if($adresse != 'null' && $adresse != null) {
            return $this->builder->where('adresse', $adresse);
        }
    }

    public function area_id($area_id)
    {
        if($area_id != 'null' && $area_id != null) {
            return $this->builder->where('area_id', $area_id);
        }
    }

    public function phone($phone)
    {
        if($phone != 'null' && $phone != null) {
            return $this->builder->where('phone', $phone);
        }
    }

    public function code($code)
    {
        if($code != 'null' && $code != null) {
            return $this->builder->where('code', $code);
        }
    }

    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('code', 'LIKE', '%' . $search . '%')
            ->orWhere('phone', 'LIKE', '%' . $search . '%')
            ->orWhereHas('area', function($q) use($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}
