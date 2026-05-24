<?php

namespace Modules\Core\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Core\Repositories\Country\CountryInterface;
use Modules\Core\Repositories\Country\CountryRepository;
use Modules\Core\Repositories\City\CityInterface;
use Modules\Core\Repositories\City\CityRepository;
use Modules\Core\Repositories\Area\AreaInterface;
use Modules\Core\Repositories\Area\AreaRepository;
use Modules\Core\Repositories\Branch\BranchInterface;
use Modules\Core\Repositories\Branch\BranchRepository;
use Modules\Core\Repositories\RoleAndPermission\RoleInterface;
use Modules\Core\Repositories\RoleAndPermission\RoleRepository;
use Modules\Core\Repositories\RoleAndPermission\PermissionInterface;
use Modules\Core\Repositories\RoleAndPermission\PermissionRepository;

class CoreServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Core';
    protected string $nameLower = 'core';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        // Bind Repository Interfaces to their Implementations
        $this->app->bind(CountryInterface::class,    CountryRepository::class);
        $this->app->bind(CityInterface::class,       CityRepository::class);
        $this->app->bind(AreaInterface::class,       AreaRepository::class);
        $this->app->bind(BranchInterface::class,     BranchRepository::class);
        $this->app->bind(RoleInterface::class,       RoleRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
    }
}
