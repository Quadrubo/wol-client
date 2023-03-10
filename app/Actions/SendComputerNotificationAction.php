<?php

namespace App\Actions;

use App\Models\Computer;
use App\Notifications\UseComputerNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class SendComputerNotificationAction
{
    public function execute(Computer $computer, int $countBefore, int $countAfter)
    {
        if ($computer->users()->count() !== $countAfter) {
            return;
        }

        // Cancel if the count neither went to 0 or from 0
        if (! (($countBefore === 1 && $countAfter === 0) || ($countBefore === 0 && $countAfter === 1))) {
            return;
        }

        if (config('services.discord.webhook_url')) {
            Notification::route('webhook', config('services.discord.webhook_url'))
                ->notify(new UseComputerNotification(
                    computerName: $computer->name,
                    computerInUse: $computer->isInUse(),
                    timestamp: Carbon::now(),
                ));
        }
    }
}
