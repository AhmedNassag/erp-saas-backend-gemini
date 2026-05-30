<?php

namespace Modules\Landlord\Repositories\Subscription;

interface SubscriptionInterface
{
    public function index($request);
    public function show($subscription);
    public function cancel($subscription);
    public function renew($subscription, $request);
}
