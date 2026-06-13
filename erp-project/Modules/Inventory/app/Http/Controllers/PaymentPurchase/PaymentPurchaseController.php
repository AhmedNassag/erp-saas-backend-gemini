<?php

namespace Modules\Inventory\Http\Controllers\PaymentPurchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\PaymentPurchase\StoreRequest;
use Modules\Inventory\Http\Requests\PaymentPurchase\UpdateRequest;
use Modules\Inventory\Repositories\PaymentPurchase\PaymentPurchaseInterface;
use Modules\Inventory\Filters\PaymentPurchase\PaymentPurchaseFilter;

class PaymentPurchaseController extends Controller
{
    protected $paymentPurchase;

    public function __construct(PaymentPurchaseInterface $paymentPurchase)
    {
        $this->paymentPurchase = $paymentPurchase;

        $this->middleware('permission:read-payment-purchase,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-payment-purchase,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-payment-purchase,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-payment-purchase,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-payment-purchase,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, PaymentPurchaseFilter $filter)
    {
        return $this->paymentPurchase->index($request, $filter);
    }

    public function show($id)
    {
        return $this->paymentPurchase->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->paymentPurchase->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->paymentPurchase->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->paymentPurchase->destroy($id);
    }
}
