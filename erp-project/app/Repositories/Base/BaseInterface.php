<?php

namespace App\Repositories\Base;

interface BaseInterface
{
    public function index($request, $filter = null);

    public function show($id);

    public function store($request);

    public function update($id, $request);

    public function destroy($id);

    public function changeStatus($id, $request);
}
