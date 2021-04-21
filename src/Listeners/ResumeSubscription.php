<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;

class ResumeSubscription
{
    public function handle(InvoicePaid $event): void
    {
        $subscription = $event->invoice->subscription;

        if ($subscription->cancelled()) {
            $subscription->resume();
        }
    }
}
