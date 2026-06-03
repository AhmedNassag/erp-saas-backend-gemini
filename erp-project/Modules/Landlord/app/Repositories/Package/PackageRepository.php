<?php

namespace Modules\Landlord\Repositories\Package;

use Modules\Landlord\Models\Package;
use Modules\Landlord\Repositories\BaseRepository;
use App\Traits\API;

class PackageRepository extends BaseRepository implements PackageInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Package();
    }

    public function index($request)
    {
        $perPage = $request['per_page'] ?? 15;
        $data = $perPage == -1
            ? $this->getModel()->orderBy('created_at', 'desc')->get()
            : $this->getModel()->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)->isOk(__('Packages'))
            ->setData($perPage == -1 ? $data : (new API)->api_model_set_paginate($data, $data))
            ->build();
    }

    public function store($request)
    {
        try {
            $this->getModel()->create($request->validated());
            return (new API)->isOk(__('Stored Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function show($package, array $with = [])
    {
        return (new API)->isOk(__('Package Data'))->setData($package)->build();
    }

    public function update($package, $data = [])
    {
        try {
            $package->update($data instanceof \Illuminate\Http\Request ? $data->validated() : $data);
            return (new API)->isOk(__('Updated Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function destroy($package)
    {
        if ($package->tenants()->count() > 0) {
            return (new API)->isError(__('Can Not Delete Because There Is A Related Data'))->setStatus(422)->build();
        }
        $package->delete();
        return (new API)->isOk(__('Destroyed Successfully'))->build();
    }
}
