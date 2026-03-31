<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Wallet;
use App\Models\ScratchCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or get test customer
        $customer = Customer::updateOrCreate(
            ['mobile' => '1234567890'],
            [
                'name' => 'Demo Customer',
                'email' => 'demo@bellavella.com',
                'status' => 'Active',
                'joined' => now(),
            ]
        );

        // 2. Ensure Coin Wallet exists
        $wallet = Wallet::firstOrCreate(
            [
                'holder_id' => $customer->id,
                'holder_type' => 'customer',
                'type' => 'coin'
            ],
            [
                'balance' => 0,
                'version' => 0
            ]
        );

        // 3. Add initial transactions if wallet is empty
        if ($wallet->balance == 0) {
            $wallet->credit(100, 'signup', 'Welcome bonus for joining Bellavella');
            $wallet->credit(50, 'referral', 'Reward for referring a friend');
        }

        // 4. Create Scratch Cards
        // Unscratched Cards
        ScratchCard::create([
            'customer_id'  => $customer->id,
            'amount'       => 20,
            'title'        => 'Lucky Reward',
            'description'  => 'Scratch to reveal your prize!',
            'is_scratched' => false,
            'source'       => 'booking_complete',
        ]);

        ScratchCard::create([
            'customer_id'  => $customer->id,
            'amount'       => 50,
            'title'        => 'Mega Prize',
            'description'  => 'Big rewards waiting for you.',
            'is_scratched' => false,
            'source'       => 'milestone_reached',
        ]);

        // Scratched Card (already in history)
        if (ScratchCard::where('customer_id', $customer->id)->where('is_scratched', true)->count() == 0) {
            $scratchedCard = ScratchCard::create([
                'customer_id'  => $customer->id,
                'amount'       => 15,
                'title'        => 'First Win',
                'description'  => 'Your first reward!',
                'is_scratched' => true,
                'scratched_at' => now(),
                'source'       => 'signup_bonus',
            ]);
            
            // Note: In a real flow, scratching the card triggers the credit. 
            // Since this is a seeder, we'll manually ensure it's reflected if needed, 
            // but credit() was already called above for bonuses.
        }

        $this->command->info('✅ ClientTestSeeder completed for Demo Customer (1234567890)');
    }
}
