<?php

namespace Modules\Core\RoleAndPermission\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Modules\Core\RoleAndPermission\App\Http\Requests\Permission\StoreRequest;
use Modules\Core\RoleAndPermission\App\Http\Requests\Permission\UpdateRequest;
use Modules\Core\RoleAndPermission\App\Models\Permission;
use Modules\Core\RoleAndPermission\App\Repositories\PermissionInterface;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct(PermissionInterface $permission)
    {
        $this->permission = $permission;

        $this->middleware('permission:read-permission', ['only' => ['index']]);
    }



    public function index(Request $request)
    {
        return $this->permission->index($request);
    }
}
