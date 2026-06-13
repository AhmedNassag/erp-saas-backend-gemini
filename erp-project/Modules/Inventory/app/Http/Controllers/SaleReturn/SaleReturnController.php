<?php

namespace Modules\Inventory\Http\Controllers\SaleReturn;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Models\SaleReturn\SaleReturn;
use Modules\Inventory\Http\Requests\SaleReturn\StoreRequest;
use Modules\Inventory\Http\Requests\SaleReturn\UpdateRequest;
use Modules\Inventory\Repositories\SaleReturn\SaleReturnInterface;
use Modules\Inventory\Filters\SaleReturn\SaleReturnFilter;

class SaleReturnController extends Controller
{
    protected $saleReturn;

    public function __construct(SaleReturnInterface $saleReturn)
    {
        $this->saleReturn = $saleReturn;

        $this->middleware('permission:read-sale-return,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-sale-return,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-sale-return,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-sale-return,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-sale-return,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:show-sale-return,tenant', ['only' => ['downloadPDF']]);
    }

    public function index(Request $request, SaleReturnFilter $filter)
    {
        return $this->saleReturn->index($request, $filter);
    }

    public function show($id)
    {
        return $this->saleReturn->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->saleReturn->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->saleReturn->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->saleReturn->destroy($id);
    }

    public function downloadPDF($id)
    {
        $saleReturn = SaleReturn::with(['saleReturnDetails', 'client', 'warehouse', 'user', 'paymentSaleReturns'])->findOrFail($id);

        $details = [];
        foreach ($saleReturn->saleReturnDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;
            $details[] = [
                'name'     => $product ? $product->name : null,
                'code'     => $product ? $product->code : null,
                'price'    => $detail->price,
                'quantity' => $detail->quantity,
                'unit'     => $unit,
                'discount' => $detail->discount,
                'TaxNet'   => $detail->TaxNet,
                'total'    => $detail->total,
            ];
        }

        $subtotal = array_sum(array_column($details, 'total'));

        $pdf = Pdf::loadView('inventory::sale-return.pdf', [
            'saleReturn' => $saleReturn,
            'details'    => $details,
            'subtotal'   => $subtotal,
        ]);

        return $pdf->download("sale_return_{$saleReturn->Ref}.pdf");
    }
}
