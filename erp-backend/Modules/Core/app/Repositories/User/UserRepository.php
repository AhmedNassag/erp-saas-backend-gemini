<?php

namespace Modules\Core\Repositories\User;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\User\User;
use Modules\Core\Repositories\User\UserInterface;
use Modules\Core\Resources\User\UserResource;

class UserRepository implements UserInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new User();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Countries'))
            ->setData($perPage == -1 ? UserResource::collection($data) : (new API)->api_model_set_paginate(UserResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $user = $this->getModel()->with($with)->findOrFail($id);
        return (new API)
            ->isOk(__('User Data'))
            ->setData(UserResource::make($user))
            ->build();
    }



    public function store($request)
    {
        try {
            $data = $request->validated();
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);
            $data['password'] = Hash::make($data['password']);

            $user = $this->getModel()->create($data);

            if ($roleIds) {
                $this->syncRoles($user, $roleIds);
            }

            return (new API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError($e->getMessage())
                ->setStatus(500)
                ->build();
        }
    }



    public function update($id, $request)
    {
        try {
            $data = $request->validated();
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);

            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->getModel()->findOrFail($id);
            $user->update($data);

            $this->syncRoles($user, $roleIds);

            return (new API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError($e->getMessage())
                ->setStatus(500)
                ->build();
        }
    }



    public function destroy($id)
    {
        $user = $this->getModel()->findOrFail($id);
        $user->delete();
        
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $user = $this->getModel()->findOrFail($id);
        $user->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }



    public function profile(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user()->load('roles');

            return (new API)
                ->isOk(__('Current User Data'))
                ->setData(UserResource::make($user))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function syncRoles($user, $roles)
    {
        if ($roles) {
            $user->roles()->sync($roles);
        }
    }
}