<?php

namespace Modules\Inventory\Database\Seeders\PaymentPurchase;

use Illuminate\Database\Seeder;

class PaymentPurchaseDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['payment-purchase' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PaymentPurchaseSeeder::class,
        ]);
    }
}
