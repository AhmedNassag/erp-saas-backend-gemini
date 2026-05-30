<?php

namespace Modules\Core\Repositories\Country;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Country\Country;
use Modules\Core\Repositories\Country\CountryInterface;
use Modules\Core\Resources\Country\CountryResource;

class CountryRepository implements CountryInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Country();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Countries'))
            ->setData($perPage == -1 ? CountryResource::collection($data) : (new API)->api_model_set_paginate(CountryResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $country = $this->getModel()->with($with)->findOrFail($id);
        return (new API)
            ->isOk(__('Country Data'))
            ->setData(CountryResource::make($country))
            ->build();
    }



    public function store($request)
    {
        try {
            $country = $this->getModel()->create($request->validated());

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
            $country = $this->getModel()->findOrFail($id);
            $country->update($request->validated());

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
        $country = $this->getModel()->findOrFail($id);
        $country->delete();
        
        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $country = $this->getModel()->findOrFail($id);
        $country->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}