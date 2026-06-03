<?php

namespace Modules\Landlord\Repositories\Tenant;

interface TenantInterface
{
    public function index($request);
    public function store($request);
    public function show($tenant);
    public function update($tenant, $request);
    public function destroy($tenant);
}
