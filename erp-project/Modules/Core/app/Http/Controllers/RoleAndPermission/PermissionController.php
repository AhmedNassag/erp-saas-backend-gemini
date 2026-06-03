<?php

namespace Modules\Core\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\RoleAndPermission\Permission\StoreRequest;
use Modules\Core\Http\Requests\RoleAndPermission\Permission\UpdateRequest;
use Modules\Core\Models\RoleAndPermission\Permission;
use Modules\Core\Repositories\RoleAndPermission\PermissionInterface;

class PermissionController extends Controller
{
    protected $permission;

    public function __construct(PermissionInterface $permission)
    {
        $this->permission = $permission;

        $this->middleware('permission:read-permission,tenant', ['only' => ['index']]);
    }



    public function index(Request $request)
    {
        return $this->permission->index($request);
    }
}
