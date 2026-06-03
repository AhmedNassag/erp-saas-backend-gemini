<?php

namespace App\Repositories\Base;

use App\Traits\API;
use Illuminate\Http\JsonResponse;

abstract class BaseRepository implements BaseInterface
{
    abstract protected function getModel(): \Illuminate\Database\Eloquent\Model;

    abstract protected function getResourceClass(): string;

    protected function getPluralName(): string
    {
        return 'Data';
    }



    protected function getSingularName(): string
    {
        return 'Data';
    }



    public function index($request, $filter = null): JsonResponse
    {
        $perPage    = $request['per_page'] ?? config('myConfig.paginationCount');
        $collection = $this->getModel()->ordering($request->ordering);

        if ($filter) {
            $collection = $collection->filter($filter);
        }

        $data          = $perPage == -1 ? $collection->where('status', 1)->get() : $collection->paginate($perPage);
        $resourceClass = $this->getResourceClass();

        return (new API)
            ->isOk(__($this->getPluralName()))
            ->setData($perPage == -1 ? $resourceClass::collection($data) : (new API)->api_model_set_paginate($resourceClass::collection($data), $data))
            ->build();
    }



    public function show($id, array $with = [])
    {
        $model         = $this->getModel()->with($with)->findOrFail($id);
        $resourceClass = $this->getResourceClass();

        return (new API)
            ->isOk(__($this->getSingularName() . ' Data'))
            ->setData($resourceClass::make($model))
            ->build();
    }



    public function store($request)
    {
        try {
            $this->getModel()->create($request->validated());

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
            $model = $this->getModel()->findOrFail($id);
            $model->update($request->validated());

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
        $model = $this->getModel()->findOrFail($id);
        $model->delete();

        return (new API)
            ->isOk(__('Destroyed Successfully'))
            ->build();
    }



    public function changeStatus($id, $request)
    {
        $model = $this->getModel()->findOrFail($id);
        $model->update(['status' => $request->status]);

        return (new API)
            ->isOk(__('Status Changed Successfully'))
            ->build();
    }
}
