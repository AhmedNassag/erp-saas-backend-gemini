<?php

namespace Modules\Landlord\Repositories\Package;

interface PackageInterface
{
    public function index($request);
    public function store($request);
    public function show($package);
    public function update($package, $request);
    public function destroy($package);
}
