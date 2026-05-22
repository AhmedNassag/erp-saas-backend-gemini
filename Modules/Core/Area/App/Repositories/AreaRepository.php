<?php

namespace Modules\Core\Area\App\Repositories;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Repositories\Dashboard\BaseRepository;
use Modules\Core\Area\App\Http\Requests\StoreRequest;
use Modules\Core\Area\App\Models\Area;
use Modules\Core\Area\App\Repositories\AreaInterface;
use Modules\Core\Area\App\resources\AreasResource;
use Modules\Core\Area\App\Filters\AreaFilter;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AreaRepository extends BaseRepository implements AreaInterface
{
    public function getModel()
    {
        return new Area();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data = $perPage == -1 ? $collection->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Areas'))
            ->setData($perPage == -1 ? AreasResource::collection($data) : (new API)->api_model_set_paginate(AreasResource::collection($data), $data))
            ->build();
    }



    public function show($area)
    {
        return (new API)
            ->isOk(__('Area Data'))
            ->setData(AreasResource::make($area))
            ->build();
    }



    public function store($request)
    {
        try {
            $area = $this->getModel()->create($request->validated());
            //save image with area object

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



    public function update($area, $request)
    {
        try {
            $area->update($request->validated());
            //save new image with area object and delete old image

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



    public function destroy($area)
    {
        $area->delete();
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }
}