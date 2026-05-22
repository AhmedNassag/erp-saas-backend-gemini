<?php

namespace Modules\Core\Repositories\Country;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Repositories\Dashboard\BaseRepository;
use Modules\Core\Models\Country\Country;
use Modules\Core\Repositories\Country\CountryInterface;
use Modules\Core\Resources\Country\CountryResource;

class CountryRepository extends BaseRepository implements CountryInterface
{
    public function getModel()
    {
        return new Country();
    }



    public function index($request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $data    = $perPage == -1 ? $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->get() : $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)
            ->isOk(__('Countries'))
            ->setData($perPage == -1 ? CountryResource::collection($data) : (new API)->api_model_set_paginate(CountryResource::collection($data) ,$data))
            ->build();

    }



    public function store($request)
    {
        try {
            $country = $this->getModel()->create($request->validated());

            //save image with country object
            if ($request->hasFile('image')) {
                $country->addMediaFromRequest('image')->toMediaCollection('country');
            }

            if ($request->hasFile('images')) {
                if ($images = $request->file('images')) {
                    $country->clearMediaCollection('images');
                    foreach ($images as $image) {
                        $country->addMedia($image)->toMediaCollection('images');
                    }
                }
            }

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



    public function show($country)
    {
        return (new API)
            ->isOk(__('Country Data'))
            ->setData(CountryResource::make($country))
            ->build();
    }



    public function update($country ,$request)
    {
        try {
            $country->update($request->validated());

            //save new image with country object and delete old image
            if ($request->hasFile('image')) {
                $file_name = $country->getMedia('country')->last()->file_name;
                $img_id = $country->getMedia('country')->last()->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
                Media::find($country->getMedia('country')->last()->id)->delete();
                $country->addMediaFromRequest('image')->toMediaCollection('country');
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



    public function destroy($country)
    {
        $file_name = $country->getMedia('country')->last()->file_name ?? null;
        $img_id    = $country->getMedia('country')->last()->id ?? null;
        if($img_id && $file_name) {
            if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                unlink(public_path('storage/' . $img_id .'/'.$file_name));
            }
        }
        if($img_id) {
            Media::find($country->getMedia('country')->last()->id)->delete();
        }
        $country->delete();

        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }
}
