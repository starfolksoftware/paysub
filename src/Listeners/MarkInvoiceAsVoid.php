<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\SubscriptionCancelled;

class MarkInvoiceAsVoid
{
    public function handle(SubscriptionCancelled $event)
    {
        $invoice = $event->subscription->openInvoice();
        $invoice->void();
    }
}
