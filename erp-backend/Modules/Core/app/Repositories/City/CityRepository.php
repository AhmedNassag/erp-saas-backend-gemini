<?php

namespace Modules\Core\Repositories\City;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\City\City;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Resources\City\CityResource;

class CityRepository implements CityInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new City();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Cities'))
            ->setData($perPage == -1 ? CityResource::collection($data) : (new API)->api_model_set_paginate(CityResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $city = $this->getModel()->with($with)->findOrFail($id);
        return (new API)
            ->isOk(__('City Data'))
            ->setData(CityResource::make($city))
            ->build();
    }



    public function store($request)
    {
        try {
            $city = $this->getModel()->create($request->validated());

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
            $city = $this->getModel()->findOrFail($id);
            $city->update($request->validated());

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
        $city = $this->getModel()->findOrFail($id);
        $city->delete();
        
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $city = $this->getModel()->findOrFail($id);
        $city->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}