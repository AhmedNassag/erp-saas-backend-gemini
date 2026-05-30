<?php

namespace Modules\Core\Repositories\Branch;

use App\Traits\API;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Core\Models\Branch\Branch;
use Modules\Core\Repositories\Branch\BranchInterface;
use Modules\Core\Resources\Branch\BranchResource;

class BranchRepository implements BranchInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Branch();
    }



    public function index($request, $filter): \Illuminate\Http\JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering)->filter($filter);
        $data       = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);

        return (new API)
            ->isOk(__('Branches'))
            ->setData($perPage == -1 ? BranchResource::collection($data) : (new API)->api_model_set_paginate(BranchResource::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $branch = $this->getModel()->with($with)->findOrFail($id);
        return (new API)
            ->isOk(__('Branch Data'))
            ->setData(BranchResource::make($branch))
            ->build();
    }



    public function store($request)
    {
        try {
            $branch = $this->getModel()->create($request->validated());

            //store single image
            if ($request->hasFile('image')) {
                $branch->clearMediaCollection('branch'); //delete old record from database
                $image = $request->file('image');
                $this->uploadMedia($branch, 'branch', $image);
            }
            //store multi images
            if ($request->hasFile('images')) {
                $branch->clearMediaCollection('branch_images'); //delete old record from database
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($branch, 'branch_images', $image);
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



    public function update($id, $request)
    {
        try {
            $branch = $this->getModel()->findOrFail($id);
            $branch->update($request->validated());

            //update single image
            if ($request->hasFile('image')) {
                $branch->clearMediaCollection('branch'); //delete old record from database
                $image = $request->file('image');
                $this->uploadMedia($branch, 'branch', $image);
            }
            //update multi images
            if ($request->hasFile('images')) {
                $branch->clearMediaCollection('branch_images'); //delete old record from database
                foreach($request->file('images') as $image)
                {
                    $this->uploadMedia($branch, 'branch_images', $image);
                }
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



    public function destroy($id)
    {
        try {
            $branch = $this->getModel()->findOrFail($id);

            //delete old media if exist
            $singleMedia = $branch->getMedia('branch')->first();
            $multiMedia  = $branch->getMedia('branch_images')->all();
            if($singleMedia) {
                $branch->clearMediaCollection('branch'); //delete old record from database
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    //remove files from project
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name)))
                    {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $branch->clearMediaCollection('branch_images'); //delete old record from database
                foreach($multiMedia as $media) {
                    $file_name = $media->file_name;
                    $img_id    = $media->id;
                    if($img_id && $file_name) {
                        //remove files from branch
                        if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                            unlink(public_path('storage/' . $img_id .'/'.$file_name));
                        }
                    }
                }
            }

            $branch->delete();

            return (new API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        }
        catch (\Exception $e) {
            return (new API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    public function changeStatus($id, $request)
    {
        $branch = $this->getModel()->findOrFail($id);
        $branch->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}
