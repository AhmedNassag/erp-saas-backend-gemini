<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filters
{
    protected Builder $builder;
    protected array $var_filters = [];

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;
        $request = request();

        foreach ($this->var_filters as $filter) {
            $value = $request->query($filter) ?? $request->input($filter);
            if ($value !== null && $value !== '' && $value !== 'null') {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }
}
