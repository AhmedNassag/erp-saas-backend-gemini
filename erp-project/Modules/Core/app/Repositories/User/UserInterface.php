<?php

namespace Modules\Core\Repositories\User;

use App\Repositories\Base\BaseInterface;

interface UserInterface extends BaseInterface
{
    public function profile();
}
