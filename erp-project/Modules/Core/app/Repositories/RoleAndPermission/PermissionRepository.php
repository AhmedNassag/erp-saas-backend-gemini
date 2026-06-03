<?php

namespace Modules\Core\Repositories\RoleAndPermission;

use App\Repositories\Base\BaseRepository;
use App\Traits\API;
use Modules\Core\Models\RoleAndPermission\Permission;
use Modules\Core\Repositories\RoleAndPermission\PermissionInterface;
use Modules\Core\Resources\RoleAndPermission\PermissionResource;

class PermissionRepository extends BaseRepository implements PermissionInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Permission();
    }

    protected function getResourceClass(): string
    {
        return PermissionResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Permissions';
    }

    public function index($request, $filter = null): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');

        $query = $this->getModel()
            ->search($request['search'])
            ->orderBy('created_at', 'desc');

        if (!empty($request['module'])) {
            $modules = is_array($request['module'])
                ? $request['module']
                : explode(',', $request['module']);

            $modules = array_map('trim', $modules);

            $query->whereIn('module', $modules);
        }

        $data = $perPage == -1
            ? $query->orderBy('name', 'asc')->get()
            : $query->paginate($perPage);

        if ($perPage == -1) {
            $new_data = [];
            foreach ($data as $one) {
                $item = explode('-', $one->name);
                $action = array_shift($item);
                $model = implode('-', $item);
                $new_data[$model][$action] = new PermissionResource($one);
            }
        }

        return (new API)
            ->isOk(__('Permissions'))
            ->setData(
                $perPage == -1
                    ? $new_data
                    : (new API)->api_model_set_paginate(PermissionResource::collection($data), $data)
            )
            ->build();
    }
}
