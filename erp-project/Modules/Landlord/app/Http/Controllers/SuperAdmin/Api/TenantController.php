<?php

namespace Modules\Landlord\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Modules\Landlord\Http\Requests\Tenant\UpdateRequest;
use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Repositories\Tenant\TenantInterface;

class TenantController extends Controller
{
    protected $tenant;

    public function __construct(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        return $this->tenant->index($request);
    }

    public function show(Tenant $tenant)
    {
        return $this->tenant->show($tenant);
    }

    public function update(Tenant $tenant, UpdateRequest $request)
    {
        return $this->tenant->update($tenant, $request);
    }

    public function destroy(Tenant $tenant)
    {
        return $this->tenant->destroy($tenant);
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);
        return response()->json(['status' => 'success', 'message' => 'Tenant suspended.']);
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        return response()->json(['status' => 'success', 'message' => 'Tenant activated.']);
    }
}
