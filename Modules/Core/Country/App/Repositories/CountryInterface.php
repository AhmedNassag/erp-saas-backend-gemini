<?php

namespace Modules\Core\Country\App\Repositories;

interface CountryInterface
{
    public function index($request);
 
    public function store($request);
 
    public function show($country);

    public function update($country, $request);
    
    public function destroy($country);
}
