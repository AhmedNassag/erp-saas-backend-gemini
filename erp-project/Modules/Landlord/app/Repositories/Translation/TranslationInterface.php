<?php

namespace Modules\Landlord\Repositories\Translation;

interface TranslationInterface
{
    public function index($request);
    public function store($request);
    public function show($languageLine);
    public function update($languageLine, $request);
    public function destroy($languageLine);
    public function bulkUpdate($request);
}
