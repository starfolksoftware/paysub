<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\SubscriptionCreated;

class GenerateFirstInvoice
{
    public function handle(SubscriptionCreated $event): void
    {
        $subscription = $event->subscription;

        $subscription->owner->generateUpcomingInvoice();
    }
}
