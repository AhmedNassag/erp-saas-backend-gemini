<?php

namespace Modules\Landlord\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $connection = 'landlord';
    protected $fillable = ['code', 'name', 'native_name', 'direction', 'is_default', 'is_active'];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean'];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_code', 'code');
    }

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }

    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->get();
    }
}
