<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;

class MarkInvoiceAsPaid
{
    public function handle(InvoicePaid $event)
    {
        $event->invoice->markAsPaid();
    }
}
