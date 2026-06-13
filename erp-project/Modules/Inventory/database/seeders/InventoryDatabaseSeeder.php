<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Client\ClientDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Provider\ProviderDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Category\CategoryDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Brand\BrandDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Currency\CurrencyDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Unit\UnitDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Setting\SettingDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Product\ProductDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Adjustment\AdjustmentDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Transfer\TransferDatabaseSeeder;
use Modules\Inventory\Database\Seeders\ExpenseCategory\ExpenseCategoryDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Expense\ExpenseDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Purchase\PurchaseDatabaseSeeder;
use Modules\Inventory\Database\Seeders\PaymentPurchase\PaymentPurchaseDatabaseSeeder;
use Modules\Inventory\Database\Seeders\PurchaseReturn\PurchaseReturnDatabaseSeeder;
use Modules\Inventory\Database\Seeders\PaymentPurchaseReturn\PaymentPurchaseReturnDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Sale\SaleDatabaseSeeder;
use Modules\Inventory\Database\Seeders\PaymentSale\PaymentSaleDatabaseSeeder;
use Modules\Inventory\Database\Seeders\SaleReturn\SaleReturnDatabaseSeeder;
use Modules\Inventory\Database\Seeders\PaymentSaleReturn\PaymentSaleReturnDatabaseSeeder;

use Modules\Core\Models\RoleAndPermission\Role;
use Modules\Core\Models\RoleAndPermission\Permission;

class InventoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ClientDatabaseSeeder::class,
            ProviderDatabaseSeeder::class,
            CategoryDatabaseSeeder::class,
            BrandDatabaseSeeder::class,
            CurrencyDatabaseSeeder::class,
            UnitDatabaseSeeder::class,
            SettingDatabaseSeeder::class,
            ProductDatabaseSeeder::class,
            AdjustmentDatabaseSeeder::class,
            TransferDatabaseSeeder::class,
            ExpenseCategoryDatabaseSeeder::class,
            ExpenseDatabaseSeeder::class,
            PurchaseDatabaseSeeder::class,
            PaymentPurchaseDatabaseSeeder::class,
            PurchaseReturnDatabaseSeeder::class,
            PaymentPurchaseReturnDatabaseSeeder::class,
            SaleDatabaseSeeder::class,
            PaymentSaleDatabaseSeeder::class,
            SaleReturnDatabaseSeeder::class,
            PaymentSaleReturnDatabaseSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'tenant')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
