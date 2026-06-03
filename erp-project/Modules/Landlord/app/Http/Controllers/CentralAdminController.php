<?php

namespace Modules\Landlord\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CentralAdminController extends Controller
{
    public function index()
    {
        $tenantsCount = DB::connection('landlord')->table('tenants')->count();
        $packagesCount = DB::connection('landlord')->table('packages')->count();
        return view('landlord::admin.dashboard', compact('tenantsCount', 'packagesCount'));
    }

    public function tenants()
    {
        $tenants = DB::connection('landlord')->table('tenants')->get();
        return view('landlord::admin.tenants', compact('tenants'));
    }

    public function packages()
    {
        $packages = DB::connection('landlord')->table('packages')->get();
        return view('landlord::admin.packages', compact('packages'));
    }

    public function payments()
    {
        return view('landlord::admin.payments');
    }
}