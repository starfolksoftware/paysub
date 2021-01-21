<?php

namespace StarfolkSoftware\Paysub\Listeners;

use Illuminate\Support\Facades\Mail;
use StarfolkSoftware\Paysub\Events\InvoicePaid;
use StarfolkSoftware\Paysub\Mail\InvoiceMail;

class SendInvoiceNotification
{
    /**
     * Handle the event.
     *
     * @param  InvoicePaid  $event
     * @return void
     */
    public function handle(InvoicePaid $event)
    {
        $invoice = $event->invoice;
        $subscriber = $invoice->subscription->subscriber;
        $emails = $subscriber->invoiceMailables();

        foreach ($emails as $recipient) {
            Mail::to($recipient)->queue(new InvoiceMail($subscriber, $invoice));
        }
    }
}
