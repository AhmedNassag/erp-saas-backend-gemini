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
use Modules\Inventory\Repositories\Currency\CurrencyInterface;
use Modules\Inventory\Repositories\Currency\CurrencyRepository;
use Modules\Inventory\Repositories\Unit\UnitInterface;
use Modules\Inventory\Repositories\Unit\UnitRepository;
use Modules\Inventory\Repositories\Setting\SettingInterface;
use Modules\Inventory\Repositories\Setting\SettingRepository;
use Modules\Inventory\Repositories\Product\ProductInterface;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Inventory\Repositories\ProductVariant\ProductVariantRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Adjustment\AdjustmentInterface;
use Modules\Inventory\Repositories\Adjustment\AdjustmentRepository;
use Modules\Inventory\Repositories\AdjustmentDetail\AdjustmentDetailRepository;
use Modules\Inventory\Repositories\Transfer\TransferInterface;
use Modules\Inventory\Repositories\Transfer\TransferRepository;
use Modules\Inventory\Repositories\TransferDetail\TransferDetailRepository;
use Modules\Inventory\Repositories\ExpenseCategory\ExpenseCategoryInterface;
use Modules\Inventory\Repositories\ExpenseCategory\ExpenseCategoryRepository;
use Modules\Inventory\Repositories\Expense\ExpenseInterface;
use Modules\Inventory\Repositories\Expense\ExpenseRepository;
use Modules\Inventory\Repositories\Purchase\PurchaseInterface;
use Modules\Inventory\Repositories\Purchase\PurchaseRepository;
use Modules\Inventory\Repositories\PurchaseDetail\PurchaseDetailRepository;
use Modules\Inventory\Repositories\PaymentPurchase\PaymentPurchaseInterface;
use Modules\Inventory\Repositories\PaymentPurchase\PaymentPurchaseRepository;

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
        $this->app->bind(CurrencyInterface::class, CurrencyRepository::class);
        $this->app->bind(UnitInterface::class, UnitRepository::class);
        $this->app->bind(SettingInterface::class, SettingRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(ProductVariantRepository::class);
        $this->app->bind(ProductWarehouseRepository::class);
        $this->app->bind(AdjustmentInterface::class, AdjustmentRepository::class);
        $this->app->bind(AdjustmentDetailRepository::class);
        $this->app->bind(TransferInterface::class, TransferRepository::class);
        $this->app->bind(TransferDetailRepository::class);
        $this->app->bind(ExpenseCategoryInterface::class, ExpenseCategoryRepository::class);
        $this->app->bind(ExpenseInterface::class, ExpenseRepository::class);
        $this->app->bind(PurchaseInterface::class, PurchaseRepository::class);
        $this->app->bind(PurchaseDetailRepository::class);
        $this->app->bind(PaymentPurchaseInterface::class, PaymentPurchaseRepository::class);
    }
}
