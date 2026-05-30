<?php

namespace Modules\Landlord\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Modules\Landlord\Models\Subscription;
use Modules\Landlord\Repositories\Subscription\SubscriptionInterface;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscription;

    public function __construct(SubscriptionInterface $subscription)
    {
        $this->subscription = $subscription;
    }

    public function index(Request $request)
    {
        return $this->subscription->index($request);
    }

    public function show(Subscription $subscription)
    {
        return $this->subscription->show($subscription);
    }

    public function cancel(Subscription $subscription)
    {
        return $this->subscription->cancel($subscription);
    }

    public function renew(Request $request, Subscription $subscription)
    {
        return $this->subscription->renew($subscription, $request);
    }
}
