<?php

namespace Modules\Inventory\Http\Controllers\PaymentPurchaseReturn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\PaymentPurchaseReturn\StoreRequest;
use Modules\Inventory\Http\Requests\PaymentPurchaseReturn\UpdateRequest;
use Modules\Inventory\Repositories\PaymentPurchaseReturn\PaymentPurchaseReturnInterface;
use Modules\Inventory\Filters\PaymentPurchaseReturn\PaymentPurchaseReturnFilter;

class PaymentPurchaseReturnController extends Controller
{
    protected $paymentPurchaseReturn;

    public function __construct(PaymentPurchaseReturnInterface $paymentPurchaseReturn)
    {
        $this->paymentPurchaseReturn = $paymentPurchaseReturn;

        $this->middleware('permission:read-payment-purchase-return,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-payment-purchase-return,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-payment-purchase-return,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-payment-purchase-return,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-payment-purchase-return,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, PaymentPurchaseReturnFilter $filter)
    {
        return $this->paymentPurchaseReturn->index($request, $filter);
    }

    public function show($id)
    {
        return $this->paymentPurchaseReturn->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->paymentPurchaseReturn->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->paymentPurchaseReturn->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->paymentPurchaseReturn->destroy($id);
    }
}
