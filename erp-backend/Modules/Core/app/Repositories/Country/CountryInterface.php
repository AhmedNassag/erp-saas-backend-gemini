<?php

namespace Modules\Core\Repositories\Country;

interface CountryInterface
{
    public function index($request);
 
    public function store($request);
 
    public function show($country);

    public function update($country, $request);
    
    public function destroy($country);
}
