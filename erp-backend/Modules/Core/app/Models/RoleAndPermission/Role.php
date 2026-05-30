<?php

namespace Modules\Core\Models\RoleAndPermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;
use Spatie\Permission\Models\Role as MasterRole;
use App\Traits\ActivityLogTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\User\User;

class Role extends MasterRole
{
    protected $connection = 'tenant';

    use HasFactory ,Searchable, SoftDeletes/*, HasTranslations*/;

    use ActivityLogTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable  = [];
    protected $guarded   = ['id'];

    ////////////////////////////// start search with relations models //////////////////////////////
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
    ////////////////////////////// end search with relations models //////////////////////////////



    //start relations
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_ids');
    }
}
