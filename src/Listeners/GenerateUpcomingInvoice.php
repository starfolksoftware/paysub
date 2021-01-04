<?php 

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;

class GenerateUpcomingInvoice {
    public function handle(InvoicePaid $event) {
        $subscription = $event->invoice->subscription;

        $subscription->generateUpcomingInvoice();
    }
}
