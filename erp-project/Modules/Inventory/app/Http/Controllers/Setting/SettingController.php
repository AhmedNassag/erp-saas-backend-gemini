<?php

namespace Modules\Inventory\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Setting\StoreRequest;
use Modules\Inventory\Http\Requests\Setting\UpdateRequest;
use Modules\Inventory\Repositories\Setting\SettingInterface;
use Modules\Inventory\Filters\Setting\SettingFilter;

class SettingController extends Controller
{
    protected $setting;

    public function __construct(SettingInterface $setting)
    {
        $this->setting = $setting;

        $this->middleware('permission:read-setting,tenant', ['only' => ['index']]);
        $this->middleware('permission:show-setting,tenant', ['only' => ['show']]);
        $this->middleware('permission:create-setting,tenant', ['only' => ['store']]);
        $this->middleware('permission:update-setting,tenant', ['only' => ['update']]);
    }

    public function index(Request $request, SettingFilter $filter)
    {
        return $this->setting->index($request, $filter);
    }

    public function show($id)
    {
        return $this->setting->show($id);
    }

    public function store(StoreRequest $request)
    {
        return $this->setting->store($request);
    }

    public function update($id, UpdateRequest $request)
    {
        return $this->setting->update($id, $request);
    }
}
