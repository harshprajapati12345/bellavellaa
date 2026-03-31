<?php

namespace App\Helpers;

use App\Models\ScratchCard;

class RewardHelper
{
    /**
     * Generate a new scratch card for a user with weighted rewards.
     */
    public static function generateScratch($userId, $source = 'payment')
    {
        $rewards = [
            ['amount' => 10, 'chance' => 50],
            ['amount' => 20, 'chance' => 30],
            ['amount' => 50, 'chance' => 15],
            ['amount' => 100, 'chance' => 5],
        ];

        $rand = rand(1, 100);
        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += $reward['chance'];
            if ($rand <= $sum) {
                return ScratchCard::create([
                    'customer_id' => $userId,
                    'amount' => $reward['amount'],
                    'title' => "Scratch & Win",
                    'description' => "Try your luck and reveal your reward!",
                    'source' => $source,
                    'is_scratched' => false,
                ]);
            }
        }

        // Fallback to lowest reward if something goes wrong
        return ScratchCard::create([
            'customer_id' => $userId,
            'amount' => 10,
            'title' => "Scratch & Win",
            'description' => "Try your luck!",
            'source' => $source,
            'is_scratched' => false,
        ]);
    }
}
