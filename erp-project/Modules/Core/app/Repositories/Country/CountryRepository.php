<?php

namespace Modules\Core\Repositories\Country;

use App\Repositories\Base\BaseRepository;
use Modules\Core\Models\Country\Country;
use Modules\Core\Repositories\Country\CountryInterface;
use Modules\Core\Resources\Country\CountryResource;

class CountryRepository extends BaseRepository implements CountryInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Country();
    }

    protected function getResourceClass(): string
    {
        return CountryResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Countries';
    }

    protected function getSingularName(): string
    {
        return 'Country';
    }
}
