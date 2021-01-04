<?php 

namespace StarfolkSoftware\Paysub\Listeners;

use StarfolkSoftware\Paysub\Events\InvoicePaid;

class ResumeSubscription {
    public function handle(InvoicePaid $event) {
        $invoice = $event->invoice;
        $subscription = $event->invoice->subscription;

        if ($subscription->cancelled()) {
            $subscription->resume();
            $invoice->due_date = now();
            $invoice->save();
        }
    }
}
