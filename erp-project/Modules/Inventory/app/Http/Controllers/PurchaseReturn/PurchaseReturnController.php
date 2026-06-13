<?php

namespace Modules\Inventory\Http\Controllers\PurchaseReturn;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Models\PurchaseReturn\PurchaseReturn;
use Modules\Inventory\Http\Requests\PurchaseReturn\StoreRequest;
use Modules\Inventory\Http\Requests\PurchaseReturn\UpdateRequest;
use Modules\Inventory\Repositories\PurchaseReturn\PurchaseReturnInterface;
use Modules\Inventory\Filters\PurchaseReturn\PurchaseReturnFilter;

class PurchaseReturnController extends Controller
{
    protected $purchaseReturn;

    public function __construct(PurchaseReturnInterface $purchaseReturn)
    {
        $this->purchaseReturn = $purchaseReturn;

        $this->middleware('permission:read-purchase-return,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-purchase-return,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-purchase-return,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-purchase-return,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-purchase-return,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:show-purchase-return,tenant', ['only' => ['downloadPDF']]);
    }

    public function index(Request $request, PurchaseReturnFilter $filter)
    {
        return $this->purchaseReturn->index($request, $filter);
    }

    public function show($id)
    {
        return $this->purchaseReturn->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->purchaseReturn->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->purchaseReturn->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->purchaseReturn->destroy($id);
    }

    public function downloadPDF($id)
    {
        $purchaseReturn = PurchaseReturn::with(['purchaseReturnDetails', 'provider', 'warehouse', 'user', 'paymentPurchaseReturns'])->findOrFail($id);

        $details = [];
        foreach ($purchaseReturn->purchaseReturnDetails as $detail) {
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

        $pdf = Pdf::loadView('inventory::purchase-return.pdf', [
            'purchaseReturn' => $purchaseReturn,
            'details'        => $details,
            'subtotal'       => $subtotal,
        ]);

        return $pdf->download("purchase_return_{$purchaseReturn->Ref}.pdf");
    }
}
