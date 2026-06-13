<?php

namespace Modules\Inventory\Http\Controllers\PaymentSale;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Models\PaymentSale\PaymentSale;
use Modules\Inventory\Http\Requests\PaymentSale\StoreRequest;
use Modules\Inventory\Http\Requests\PaymentSale\UpdateRequest;
use Modules\Inventory\Repositories\PaymentSale\PaymentSaleInterface;
use Modules\Inventory\Filters\PaymentSale\PaymentSaleFilter;

class PaymentSaleController extends Controller
{
    protected $paymentSale;

    public function __construct(PaymentSaleInterface $paymentSale)
    {
        $this->paymentSale = $paymentSale;

        $this->middleware('permission:read-payment-sale,tenant', ['only' => ['index']]);
        $this->middleware('permission:create-payment-sale,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-payment-sale,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-payment-sale,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, PaymentSaleFilter $filter)
    {
        return $this->paymentSale->index($request, $filter);
    }

    public function show($id)
    {
        return $this->paymentSale->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->paymentSale->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->paymentSale->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->paymentSale->destroy($id);
    }

    public function downloadPDF($id)
    {
        $payment = PaymentSale::with('sale.client')->findOrFail($id);

        $pdf = Pdf::loadView('inventory::payment-sale.pdf', [
            'payment' => $payment,
        ]);

        return $pdf->download("payment_sale_{$payment->Ref}.pdf");
    }
}
