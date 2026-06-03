<?php

namespace Modules\Landlord\Repositories\Tenant;

use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Repositories\BaseRepository;
use App\Traits\API;

class TenantRepository extends BaseRepository implements TenantInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Tenant();
    }

    public function index($request)
    {
        $query = $this->getModel()->with('package');

        if ($search = $request['search']) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($status = $request['status']) {
            $query->where('status', $status);
        }

        $perPage = $request['per_page'] ?? 15;
        $data = $perPage == -1
            ? $query->orderBy('created_at', 'desc')->get()
            : $query->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)->isOk(__('Tenants'))
            ->setData($perPage == -1 ? $data : (new API)->api_model_set_paginate($data, $data))
            ->build();
    }

    public function store($request)
    {
        // Tenant creation is handled via subscribe — not used directly
        return (new API)->isError('Not implemented')->setStatus(501)->build();
    }

    public function show($tenant, array $with = [])
    {
        $tenant->load('package');
        return (new API)->isOk(__('Tenant Data'))->setData($tenant)->build();
    }

    public function update($tenant, $request)
    {
        try {
            $tenant->update($request->validated());
            return (new API)->isOk(__('Updated Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function destroy($tenant)
    {
        try {
            $tenant->delete();
            return (new API)->isOk(__('Destroyed Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }
}
