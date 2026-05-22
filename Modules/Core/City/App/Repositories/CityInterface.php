<?php

namespace Modules\Core\City\App\Repositories;

interface CityInterface
{
    public function index($request,$filter);

    public function show($city);

    public function store($request);

    public function update($city , $request);

    public function destroy($city);
}
