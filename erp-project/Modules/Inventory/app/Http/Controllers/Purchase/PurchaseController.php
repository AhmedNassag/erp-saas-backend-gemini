<?php

namespace Modules\Inventory\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Models\Purchase\Purchase;
use Modules\Inventory\Http\Requests\Purchase\StoreRequest;
use Modules\Inventory\Http\Requests\Purchase\UpdateRequest;
use Modules\Inventory\Repositories\Purchase\PurchaseInterface;
use Modules\Inventory\Filters\Purchase\PurchaseFilter;

class PurchaseController extends Controller
{
    protected $purchase;

    public function __construct(PurchaseInterface $purchase)
    {
        $this->purchase = $purchase;

        $this->middleware('permission:read-purchase,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-purchase,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-purchase,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-purchase,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-purchase,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:show-purchase,tenant', ['only' => ['downloadPDF']]);
    }

    public function index(Request $request, PurchaseFilter $filter)
    {
        return $this->purchase->index($request, $filter);
    }

    public function show($id)
    {
        return $this->purchase->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->purchase->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->purchase->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->purchase->destroy($id);
    }

    public function downloadPDF($id)
    {
        $purchase = Purchase::with(['purchaseDetails', 'provider', 'warehouse', 'user', 'paymentPurchases'])->findOrFail($id);

        $details = [];
        foreach ($purchase->purchaseDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;
            $details[] = [
                'name'     => $product ? $product->name : null,
                'code'     => $product ? $product->code : null,
                'cost'     => $detail->cost,
                'quantity' => $detail->quantity,
                'unit'     => $unit,
                'discount' => $detail->discount,
                'TaxNet'   => $detail->TaxNet,
                'total'    => $detail->total,
            ];
        }

        $subtotal = array_sum(array_column($details, 'total'));

        $pdf = Pdf::loadView('inventory::purchase.pdf', [
            'purchase' => $purchase,
            'details'  => $details,
            'subtotal' => $subtotal,
        ]);

        return $pdf->download("purchase_{$purchase->Ref}.pdf");
    }
}
