<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Customer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $customers = Customer::whereNull('referral_code')->get();

        foreach ($customers as $customer) {
            $customer->referral_code = Customer::generateUniqueReferralCode();
            $customer->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to undo unless we track which ones were generated here
    }
};
