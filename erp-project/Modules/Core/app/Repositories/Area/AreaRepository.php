<?php

namespace Modules\Core\Repositories\Area;

use App\Repositories\Base\BaseRepository;
use Modules\Core\Models\Area\Area;
use Modules\Core\Repositories\Area\AreaInterface;
use Modules\Core\Resources\Area\AreaResource;

class AreaRepository extends BaseRepository implements AreaInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Area();
    }

    protected function getResourceClass(): string
    {
        return AreaResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Areas';
    }

    protected function getSingularName(): string
    {
        return 'Area';
    }
}
