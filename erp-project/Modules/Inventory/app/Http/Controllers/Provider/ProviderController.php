<?php

namespace Modules\Inventory\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Provider\StoreRequest;
use Modules\Inventory\Http\Requests\Provider\UpdateRequest;
use Modules\Inventory\Http\Requests\Provider\ChangeStatusRequest;
use Modules\Inventory\Repositories\Provider\ProviderInterface;
use Modules\Inventory\Filters\Provider\ProviderFilter;

class ProviderController extends Controller
{
    protected $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;

        $this->middleware('permission:read-provider,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-provider,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-provider,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-provider,tenant', ['only' => ['update']]);
        $this->middleware('permission:delete-provider,tenant', ['only' => ['destroy']]);
        $this->middleware('permission:changeStatus-provider,tenant', ['only' => ['changeStatus']]);
    }

    public function index(Request $request, ProviderFilter $filter)
    {
        return $this->provider->index($request, $filter);
    }

    public function show($id)
    {
        return $this->provider->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->provider->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->provider->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->provider->destroy($id);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        return $this->provider->changeStatus($id, $request);
    }
}
