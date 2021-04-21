<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;

class GenerateUpcomingInvoice
{
    public function handle(InvoicePaid $event): void
    {
        $subscription = $event->invoice->subscription;

        $subscription->owner->generateUpcomingInvoice();
    }
}
