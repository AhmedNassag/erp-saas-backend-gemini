<?php

namespace Modules\Landlord\Repositories\Subscription;

use Modules\Landlord\Models\Subscription;
use Modules\Landlord\Models\Package;
use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Repositories\BaseRepository;
use App\Traits\API;

class SubscriptionRepository extends BaseRepository implements SubscriptionInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Subscription();
    }

    public function index($request)
    {
        $query = $this->getModel()->with(['tenant', 'package']);

        if ($status = $request['status']) {
            $query->where('status', $status);
        }

        $perPage = $request['per_page'] ?? 15;
        $data = $perPage == -1
            ? $query->orderBy('created_at', 'desc')->get()
            : $query->orderBy('created_at', 'desc')->paginate($perPage);

        return (new API)->isOk(__('Subscriptions'))
            ->setData($perPage == -1 ? $data : (new API)->api_model_set_paginate($data, $data))
            ->build();
    }

    public function show($subscription, array $with = [])
    {
        $subscription->load(['tenant', 'package']);
        return (new API)->isOk(__('Subscription Data'))->setData($subscription)->build();
    }

    public function cancel($subscription)
    {
        try {
            $subscription->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
            return (new API)->isOk(__('Subscription cancelled'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function renew($subscription, $request)
    {
        try {
            $packageId = $request['package_id'] ?? $subscription->package_id;
            $duration  = $request['duration_months'] ?? 12;

            $subscription->update([
                'status'  => 'active',
                'package_id' => $packageId,
                'ends_at' => now()->addMonths($duration),
                'cancelled_at' => null,
            ]);

            return (new API)->isOk(__('Subscription renewed'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }
}
