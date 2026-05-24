<?php

namespace Modules\Core\Repositories\Branch;

interface BranchInterface
{
    public function index($request);
 
    public function store($request);
 
    public function show($branch);

    public function update($branch, $request);
    
    public function destroy($branch);
}
