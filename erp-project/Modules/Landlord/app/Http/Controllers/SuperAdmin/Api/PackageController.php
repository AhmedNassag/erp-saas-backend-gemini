<?php

namespace Modules\Landlord\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Modules\Landlord\Http\Requests\Package\StoreRequest;
use Modules\Landlord\Http\Requests\Package\UpdateRequest;
use Modules\Landlord\Models\Package;
use Modules\Landlord\Repositories\Package\PackageInterface;

class PackageController extends Controller
{
    protected $package;

    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        return $this->package->index($request);
    }

    public function store(StoreRequest $request)
    {
        return $this->package->store($request);
    }

    public function show(Package $package)
    {
        return $this->package->show($package);
    }

    public function update(Package $package, UpdateRequest $request)
    {
        return $this->package->update($package, $request);
    }

    public function destroy(Package $package)
    {
        return $this->package->destroy($package);
    }
}
