<?php

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;
use StarfolkSoftware\Paysub\Models\Invoice;

class MarkInvoiceAsPaid
{
    public function handle(InvoicePaid $event)
    {
        $event->invoice->status = Invoice::STATUS_PAID;
        $event->invoice->save();
    }
}
