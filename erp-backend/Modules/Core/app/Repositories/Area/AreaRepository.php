<?php

namespace Modules\Core\Repositories\Area;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Area\Area;
use Modules\Core\Repositories\Area\AreaInterface;
use Modules\Core\Resources\Area\AreaResource;

class AreaRepository implements AreaInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Area();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Areas'))
            ->setData($perPage == -1 ? AreaResource::collection($data) : (new API)->api_model_set_paginate(AreaResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $area = $this->getModel()->with($with)->findOrFail($id);

        return (new API)
            ->isOk(__('Area Data'))
            ->setData(AreaResource::make($area))
            ->build();
    }



    public function store($request)
    {
        try {
            $area = $this->getModel()->create($request->validated());

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
            $area = $this->getModel()->findOrFail($id);
            $area->update($request->validated());

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
        $area = $this->getModel()->findOrFail($id);
        $area->delete();
        
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $area = $this->getModel()->findOrFail($id);
        $area->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}