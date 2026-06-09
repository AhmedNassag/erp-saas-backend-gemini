<?php

namespace Modules\Inventory\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Currency\StoreRequest;
use Modules\Inventory\Http\Requests\Currency\UpdateRequest;
use Modules\Inventory\Http\Requests\Currency\ChangeStatusRequest;
use Modules\Inventory\Repositories\Currency\CurrencyInterface;
use Modules\Inventory\Filters\Currency\CurrencyFilter;

class CurrencyController extends Controller
{
    protected $currency;

    public function __construct(CurrencyInterface $currency)
    {
        $this->currency = $currency;

        $this->middleware('permission:read-currency,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-currency,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-currency,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-currency,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-currency,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-currency,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, CurrencyFilter $filter)
    {
        return $this->currency->index($request, $filter);
    }

    public function show($id)
    {
        return $this->currency->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->currency->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->currency->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->currency->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->currency->changeStatus($id, $request);
    }
}
