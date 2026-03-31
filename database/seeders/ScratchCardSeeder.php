<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ScratchCard;
use Illuminate\Database\Seeder;

class ScratchCardSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();

        if (!$customer) {
            $this->command->info('No customer found to seed scratch cards for.');
            return;
        }

        // Clear existing unscratched cards for this customer to avoid clutter
        ScratchCard::where('customer_id', $customer->id)
            ->where('is_scratched', false)
            ->delete();

        $cards = [
            [
                'customer_id' => $customer->id,
                'amount' => 50,
                'title' => 'Welcome Reward',
                'description' => 'Thanks for joining BellaVella!',
                'is_scratched' => false,
                'source' => 'welcome',
            ],
            [
                'customer_id' => $customer->id,
                'amount' => 20,
                'title' => 'First Booking Bonus',
                'description' => 'A small gift for your first booking.',
                'is_scratched' => false,
                'source' => 'booking',
            ],
        ];

        foreach ($cards as $card) {
            ScratchCard::create($card);
        }

        $this->command->info('Scratch cards seeded for customer: ' . $customer->phone);
    }
}
