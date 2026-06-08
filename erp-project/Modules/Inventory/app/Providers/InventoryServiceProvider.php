<?php

namespace Modules\Inventory\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Inventory\Repositories\Client\ClientInterface;
use Modules\Inventory\Repositories\Client\ClientRepository;
use Modules\Inventory\Repositories\Provider\ProviderInterface;
use Modules\Inventory\Repositories\Provider\ProviderRepository;
use Modules\Inventory\Repositories\Category\CategoryInterface;
use Modules\Inventory\Repositories\Category\CategoryRepository;
use Modules\Inventory\Repositories\Brand\BrandInterface;
use Modules\Inventory\Repositories\Brand\BrandRepository;

class InventoryServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Inventory';
    protected string $nameLower = 'inventory';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(ClientInterface::class, ClientRepository::class);
        $this->app->bind(ProviderInterface::class, ProviderRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(BrandInterface::class, BrandRepository::class);
    }
}
