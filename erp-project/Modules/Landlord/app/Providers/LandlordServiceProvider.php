<?php

namespace Modules\Landlord\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Landlord\Repositories\Package\PackageInterface;
use Modules\Landlord\Repositories\Package\PackageRepository;
use Modules\Landlord\Repositories\Tenant\TenantInterface;
use Modules\Landlord\Repositories\Tenant\TenantRepository;
use Modules\Landlord\Repositories\Language\LanguageInterface;
use Modules\Landlord\Repositories\Language\LanguageRepository;
use Modules\Landlord\Repositories\Translation\TranslationInterface;
use Modules\Landlord\Repositories\Translation\TranslationRepository;
use Modules\Landlord\Repositories\Subscription\SubscriptionInterface;
use Modules\Landlord\Repositories\Subscription\SubscriptionRepository;

class LandlordServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Landlord';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'landlord';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(PackageInterface::class,        PackageRepository::class);
        $this->app->bind(TenantInterface::class,         TenantRepository::class);
        $this->app->bind(LanguageInterface::class,       LanguageRepository::class);
        $this->app->bind(TranslationInterface::class,    TranslationRepository::class);
        $this->app->bind(SubscriptionInterface::class,   SubscriptionRepository::class);
    }
}
