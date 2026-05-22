<?php

namespace Modules\Core\Area\App\Repositories;

interface AreaInterface
{
    public function index($request,$filter);

    public function show($area);

    public function store($request);

    public function update($area , $request);

    public function destroy($area);
}
