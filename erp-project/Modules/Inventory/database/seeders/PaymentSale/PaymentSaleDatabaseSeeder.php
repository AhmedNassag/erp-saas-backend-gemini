<?php

namespace Modules\Inventory\Database\Seeders\PaymentSale;

use Illuminate\Database\Seeder;

class PaymentSaleDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['payment-sale' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PaymentSaleSeeder::class,
        ]);
    }
}
