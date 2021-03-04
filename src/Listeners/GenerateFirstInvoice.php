<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\SubscriptionCreated;

class GenerateFirstInvoice
{
    public function handle(SubscriptionCreated $event)
    {
        $subscription = $event->subscription;

        $subscription->subscriber()->generateUpcomingInvoice();
    }
}
