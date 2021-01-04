<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\SubscriptionCancelled;
use StarfolkSoftware\Paysub\Models\Invoice;

class MarkInvoiceAsVoid
{
    public function handle(SubscriptionCancelled $event)
    {
        $invoice = $event->subscription->openInvoice();
        $invoice->status = Invoice::STATUS_VOID;
        $invoice->save();
    }
}
