<?php

namespace Modules\Inventory\Http\Controllers\PaymentSaleReturn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\PaymentSaleReturn\StoreRequest;
use Modules\Inventory\Http\Requests\PaymentSaleReturn\UpdateRequest;
use Modules\Inventory\Repositories\PaymentSaleReturn\PaymentSaleReturnInterface;
use Modules\Inventory\Filters\PaymentSaleReturn\PaymentSaleReturnFilter;

class PaymentSaleReturnController extends Controller
{
    protected $paymentSaleReturn;

    public function __construct(PaymentSaleReturnInterface $paymentSaleReturn)
    {
        $this->paymentSaleReturn = $paymentSaleReturn;

        $this->middleware('permission:read-payment-sale-return,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-payment-sale-return,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-payment-sale-return,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-payment-sale-return,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-payment-sale-return,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, PaymentSaleReturnFilter $filter)
    {
        return $this->paymentSaleReturn->index($request, $filter);
    }

    public function show($id)
    {
        return $this->paymentSaleReturn->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->paymentSaleReturn->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->paymentSaleReturn->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->paymentSaleReturn->destroy($id);
    }
}
