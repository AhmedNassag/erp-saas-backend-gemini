<?php

namespace Modules\Core\Models\RoleAndPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;
use Spatie\Permission\Models\Permission as MasterPermission;

class Permission extends MasterPermission
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    protected $guarded  = ['id'];

    ////////////////////////////// start search with relations models //////////////////////////////
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
    ////////////////////////////// end search with relations models //////////////////////////////

}
