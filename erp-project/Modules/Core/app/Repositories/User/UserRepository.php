<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\Base\BaseRepository;
use App\Traits\API;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User\User;
use Modules\Core\Repositories\User\UserInterface;
use Modules\Core\Resources\User\UserResource;

class UserRepository extends BaseRepository implements UserInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new User();
    }

    protected function getResourceClass(): string
    {
        return UserResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Users';
    }

    protected function getSingularName(): string
    {
        return 'User';
    }

    public function store($request)
    {
        try {
            $data    = $request->validated();
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);

            $data['password'] = Hash::make($data['password']);

            $user = $this->getModel()->create($data);

            if ($roleIds) {
                $this->syncRoles($user, $roleIds);
            }

            return (new API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError($e->getMessage())
                ->setStatus(500)
                ->build();
        }
    }

    public function update($id, $request)
    {
        try {
            $data    = $request->validated();
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);

            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->getModel()->findOrFail($id);
            $user->update($data);

            $this->syncRoles($user, $roleIds);

            return (new API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError($e->getMessage())
                ->setStatus(500)
                ->build();
        }
    }

    public function profile(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user()->load('roles');

            return (new API)
                ->isOk(__('Current User Data'))
                ->setData(UserResource::make($user))
                ->build();
        } catch (\Exception $e) {
            return (new API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function syncRoles($user, $roles)
    {
        if ($roles) {
            $user->roles()->sync($roles);
        }
    }
}
