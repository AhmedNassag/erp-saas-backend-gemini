<?php

namespace Modules\Core\RoleAndPermission\App\Repositories;

interface RoleInterface
{
    public function index($request);

    public function show($role);

    public function store($request);

    public function update($role , $request);

    public function destroy($role);
}
