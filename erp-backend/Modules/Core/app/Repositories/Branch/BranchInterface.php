<?php

namespace Modules\Core\Repositories\Branch;

interface BranchInterface
{
    public function index($request, $filter);
 
    public function store($request);
 
    public function show($id);

    public function update($id, $request);
    
    public function destroy($id);

    public function changeStatus($id, $request);
}
