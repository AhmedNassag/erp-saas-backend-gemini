<?php

namespace Modules\Landlord\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSetting extends Model
{
    protected $connection = "landlord";
    protected $table      = "cms_settings";
    protected $fillable   = ["key", "value"];
    protected $casts      = ["value" => "array"];

    public static function get(string $key, mixed $default = null): mixed
    {
        $record = static::where("key", $key)->first();
        return $record ? $record->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(["key" => $key], ["value" => $value]);
    }
}
