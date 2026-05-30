<?php

namespace Modules\Landlord\Repositories\Language;

interface LanguageInterface
{
    public function index($request);
    public function store($request);
    public function show($language);
    public function update($language, $request);
    public function destroy($language);
}
