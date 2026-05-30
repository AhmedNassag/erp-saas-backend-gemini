<?php

namespace Modules\Core\Repositories\Department;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Department\Department;
use Modules\Core\Repositories\Department\DepartmentInterface;
use Modules\Core\Resources\Department\DepartmentResource;

class DepartmentRepository implements DepartmentInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Department();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Countries'))
            ->setData($perPage == -1 ? DepartmentResource::collection($data) : (new API)->api_model_set_paginate(DepartmentResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $department = $this->getModel()->with($with)->findOrFail($id);
        return (new API)
            ->isOk(__('Department Data'))
            ->setData(DepartmentResource::make($department))
            ->build();
    }



    public function store($request)
    {
        try {
            $department = $this->getModel()->create($request->validated());

            return (new API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function update($id, $request)
    {
        try {
            $department = $this->getModel()->findOrFail($id);
            $department->update($request->validated());

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



    public function destroy($id)
    {
        $department = $this->getModel()->findOrFail($id);
        $department->delete();
        
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $department = $this->getModel()->findOrFail($id);
        $department->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}