<?php
namespace Modules\Core\Repositories\RoleAndPermission;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Models\User;
use Modules\Landlord\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\RoleAndPermission\Role;
use Modules\Core\Models\RoleAndPermission\Permission;
use Modules\Core\Repositories\RoleAndPermission\RoleInterface;
use Modules\Core\Resources\RoleAndPermission\RoleResource;

class RoleRepository implements RoleInterface
{

    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Role();
    }



    public function index($request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $data = $perPage == -1 ? $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->get() : $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)
            ->isOk(__('Roles'))
            ->setData($perPage == -1 ? RoleResource::collection($data) : (new API)->api_model_set_paginate(RoleResource::collection($data) ,$data))
            ->build();

    }



    public function show($role)
    {
        return (new API)
            ->isOk(__('Role Data'))
            ->setData(RoleResource::make($role))
            ->build();
    }



    public function store($request)
    {
        try {
            // append guard_name = 'tenant' to request validated data to be used in create role and assign permissions
            $requestData = array_merge($request->validated(), ['guard_name' => 'tenant']);
            $role = $this->getModel()->create($requestData);

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