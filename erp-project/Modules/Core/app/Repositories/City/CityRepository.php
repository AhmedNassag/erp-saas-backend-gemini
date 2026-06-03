<?php

namespace Modules\Core\Repositories\City;

use App\Repositories\Base\BaseRepository;
use Modules\Core\Models\City\City;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Resources\City\CityResource;

class CityRepository extends BaseRepository implements CityInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new City();
    }

    protected function getResourceClass(): string
    {
        return CityResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Cities';
    }

    protected function getSingularName(): string
    {
        return 'City';
    }
}
