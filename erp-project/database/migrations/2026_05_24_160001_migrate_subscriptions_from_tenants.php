<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        $tenants = DB::connection('landlord')->table('tenants')->get();

        foreach ($tenants as $tenant) {
            $status = $tenant->status === 'suspended' ? 'expired' : 'active';

            $exists = DB::connection('landlord')->table('subscriptions')
                ->where('tenant_id', $tenant->id)
                ->exists();

            if (!$exists) {
                DB::connection('landlord')->table('subscriptions')->insert([
                    'tenant_id'     => $tenant->id,
                    'package_id'    => $tenant->package_id,
                    'status'        => $status,
                    'trial_ends_at' => null,
                    'starts_at'     => $tenant->created_at,
                    'ends_at'       => $tenant->subscription_ends_at ?? now()->addYear(),
                    'cancelled_at'  => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::connection('landlord')->table('subscriptions')->truncate();
    }
};
