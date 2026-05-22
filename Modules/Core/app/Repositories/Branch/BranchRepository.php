<?php

namespace Modules\Core\Repositories\Branch;

use App\Http\Responses\ApiResponse;
use App\Traits\API;
use App\Repositories\Dashboard\BaseRepository;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Branch\Branch;
use Modules\Core\Repositories\Branch\BranchInterface;
use Modules\Core\Resources\Branch\BranchResource;

class BranchRepository extends BaseRepository implements BranchInterface
{
    public function getModel()
    {
        return new Branch();
    }



    public function index($request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request['per_page'] ?? config('myConfig.paginationCount');
        $data    = $perPage == -1 ? $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->get() : $this->getModel()->search($request['search'])->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)
            ->isOk(__('Countries'))
            ->setData($perPage == -1 ? BranchResource::collection($data) : (new API)->api_model_set_paginate(BranchResource::collection($data) ,$data))
            ->build();

    }



    public function store($request)
    {
        try {
            $branch = $this->getModel()->create($request->validated());

            //save image with branch object
            if ($request->hasFile('image')) {
                $branch->addMediaFromRequest('image')->toMediaCollection('branch');
            }

            if ($request->hasFile('images')) {
                if ($images = $request->file('images')) {
                    $branch->clearMediaCollection('images');
                    foreach ($images as $image) {
                        $branch->addMedia($image)->toMediaCollection('images');
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



    public function show($branch)
    {
        return (new API)
            ->isOk(__('Branch Data'))
            ->setData(BranchResource::make($branch))
            ->build();
    }



    public function update($branch ,$request)
    {
        try {
            $branch->update($request->validated());

            //save new image with branch object and delete old image
            if ($request->hasFile('image')) {
                $file_name = $branch->getMedia('branch')->last()->file_name;
                $img_id = $branch->getMedia('branch')->last()->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
                Media::find($branch->getMedia('branch')->last()->id)->delete();
                $branch->addMediaFromRequest('image')->toMediaCollection('branch');
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



    public function destroy($branch)
    {
        $file_name = $branch->getMedia('branch')->last()->file_name ?? null;
        $img_id    = $branch->getMedia('branch')->last()->id ?? null;
        if($img_id && $file_name) {
            if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                unlink(public_path('storage/' . $img_id .'/'.$file_name));
            }
        }
        if($img_id) {
            Media::find($branch->getMedia('branch')->last()->id)->delete();
        }
        $branch->delete();

        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }
}
