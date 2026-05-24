<?php

namespace Modules\Core\Repositories\Area;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Area\Area;
use Modules\Core\Repositories\Area\AreaInterface;
use Modules\Core\Resources\Area\AreasResource;

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