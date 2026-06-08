<?php

namespace Modules\Inventory\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Client\StoreRequest;
use Modules\Inventory\Http\Requests\Client\UpdateRequest;
use Modules\Inventory\Http\Requests\Client\ChangeStatusRequest;
use Modules\Inventory\Repositories\Client\ClientInterface;
use Modules\Inventory\Filters\Client\ClientFilter;

class ClientController extends Controller
{
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        $this->middleware('permission:read-client,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-client,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-client,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-client,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-client,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-client,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, ClientFilter $filter)
    {
        return $this->client->index($request, $filter);
    }

    public function show($id)
    {
        return $this->client->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->client->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->client->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->client->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->client->changeStatus($id, $request);
    }
}
