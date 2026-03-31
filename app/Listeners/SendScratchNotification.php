<?php

namespace App\Listeners;

use App\Events\ScratchCardCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendScratchNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(ScratchCardCreated $event): void
    {
        $user = $event->user;
        $card = $event->card;

        if (!$user || !$user->fcm_token) {
            return;
        }

        try {
            app(\App\Services\FcmService::class)->sendPush(
                token: $user->fcm_token,
                title: "🎁 You got a reward!",
                body: "Congrats! You earned a scratch card. Tap to scratch & win now!",
                data: [
                    'type' => 'scratch_card',
                    'card_id' => (string) $card->id,
                ]
            );
        } catch (\Exception $e) {
            \Log::error("Failed to send scratch notification: " . $e->getMessage());
        }
    }

}
