<?php

namespace Modules\Core\Repositories\City;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\City\City;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Resources\City\CityResource;

class CityRepository extends BaseRepository implements CityInterface
{
    public function getModel()
    {
        return new City();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data = $perPage == -1 ? $collection->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Cities'))
            ->setData($perPage == -1 ? CityResource::collection($data) : (new API)->api_model_set_paginate(CityResource::collection($data), $data))
            ->build();
    }



    public function show($city)
    {
        return (new API)
            ->isOk(__('City Data'))
            ->setData(CityResource::make($city))
            ->build();
    }



    public function store($request)
    {
        try {
            $city = $this->getModel()->create($request->validated());
            //save image with city object

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



    public function update($city, $request)
    {
        try {
            $city->update($request->validated());
            //save new image with city object and delete old image

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



    public function destroy($city)
    {
        $city->delete();
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }
}