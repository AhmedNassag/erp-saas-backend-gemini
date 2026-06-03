<?php

namespace Modules\Core\Repositories\Department;

use App\Repositories\Base\BaseRepository;
use Modules\Core\Models\Department\Department;
use Modules\Core\Repositories\Department\DepartmentInterface;
use Modules\Core\Resources\Department\DepartmentResource;

class DepartmentRepository extends BaseRepository implements DepartmentInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Department();
    }

    protected function getResourceClass(): string
    {
        return DepartmentResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Departments';
    }

    protected function getSingularName(): string
    {
        return 'Department';
    }
}
