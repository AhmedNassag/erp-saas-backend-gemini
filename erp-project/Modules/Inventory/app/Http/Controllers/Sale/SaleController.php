<?php

namespace Modules\Inventory\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Models\Sale\Sale;
use Modules\Inventory\Http\Requests\Sale\StoreRequest;
use Modules\Inventory\Http\Requests\Sale\UpdateRequest;
use Modules\Inventory\Repositories\Sale\SaleInterface;
use Modules\Inventory\Filters\Sale\SaleFilter;

class SaleController extends Controller
{
    protected $sale;

    public function __construct(SaleInterface $sale)
    {
        $this->sale = $sale;

        $this->middleware('permission:read-sale,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-sale,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-sale,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-sale,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-sale,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:show-sale,tenant', ['only' => ['downloadPDF']]);
    }

    public function index(Request $request, SaleFilter $filter)
    {
        return $this->sale->index($request, $filter);
    }

    public function show($id)
    {
        return $this->sale->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->sale->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->sale->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->sale->destroy($id);
    }

    public function downloadPDF($id)
    {
        $sale = Sale::with(['saleDetails', 'client', 'warehouse', 'user', 'paymentSales'])->findOrFail($id);

        $details = [];
        foreach ($sale->saleDetails as $detail) {
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

        $pdf = Pdf::loadView('inventory::sale.pdf', [
            'sale'     => $sale,
            'details'  => $details,
            'subtotal' => $subtotal,
        ]);

        return $pdf->download("sale_{$sale->Ref}.pdf");
    }
}
