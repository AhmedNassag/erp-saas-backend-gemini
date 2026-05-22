<?php

namespace Modules\Core\RoleAndPermission\App\Repositories;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Modules\Core\RoleAndPermission\App\Http\Requests\StoreRequest;
use Modules\Core\RoleAndPermission\App\Models\Role;
use Modules\Core\RoleAndPermission\App\Models\Permission;
use App\Repositories\Dashboard\BaseRepository;
use Modules\Core\RoleAndPermission\App\Repositories\RoleInterface;
use Modules\Core\RoleAndPermission\App\resources\RolesResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\AdditionalDataTrait;

class RoleRepository extends BaseRepository implements RoleInterface
{
    use AdditionalDataTrait;

    public function getModel()
    {
        return new Role();
    }



    public function index($request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $data = $perPage == -1 ? $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->get() : $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)
            ->isOk(__('Roles'))
            ->setData($perPage == -1 ? RolesResource::collection($data) : (new API)->api_model_set_paginate(RolesResource::collection($data) ,$data))
            ->build();

    }



    public function show($role)
    {
        return (new API)
            ->isOk(__('Role Data'))
            ->setData(RolesResource::make($role))
            ->build();
    }



    public function store($request)
    {
        try {
            $role = $this->getModel()->create($request->validated());

            if($request->permission_ids){
                $permissions = Permission::whereIN('id',$request->permission_ids)->pluck('name');
                $role->syncPermissions($permissions);
            }

            return (new API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError('An Error occurred')
                ->setStatus(500)
                ->build();
        }
    }



    public function update($role ,$request)
    {
        try {
            $role->update($request->validated());

            if($request->permission_ids){
                $permissions = Permission::whereIN('id',$request->permission_ids)->pluck('name');
                $role->syncPermissions($permissions);
            }

            return (new API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function destroy($role)
    {
        if(\DB::connection('tenant')->table("model_has_roles")->where("role_id",$role->id)->count()>0) {
            throw new \Exception(__('Can Not Delete Beacause There Is A Related Data'));
        }
        $role->delete();
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }
}
