<?php

namespace Modules\Core\Repositories\RoleAndPermission;

interface RoleInterface
{
    public function index($request);

    public function show($id);

    public function store($request);

    public function update($id, $request);

    public function destroy($id);
}
