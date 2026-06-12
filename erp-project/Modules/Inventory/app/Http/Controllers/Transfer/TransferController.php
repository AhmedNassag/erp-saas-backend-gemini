<?php

namespace Modules\Inventory\Http\Controllers\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Transfer\StoreRequest;
use Modules\Inventory\Http\Requests\Transfer\UpdateRequest;
use Modules\Inventory\Repositories\Transfer\TransferInterface;
use Modules\Inventory\Filters\Transfer\TransferFilter;

class TransferController extends Controller
{
    protected $transfer;

    public function __construct(TransferInterface $transfer)
    {
        $this->transfer = $transfer;

        $this->middleware('permission:read-transfer,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-transfer,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-transfer,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-transfer,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-transfer,tenant', ['only' => ['destroy']]);
    }

    public function index(Request $request, TransferFilter $filter)
    {
        return $this->transfer->index($request, $filter);
    }

    public function show($id)
    {
        return $this->transfer->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->transfer->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->transfer->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->transfer->destroy($id);
    }
}
