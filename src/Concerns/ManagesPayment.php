<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Events\InvoicePaid;
use StarfolkSoftware\Paysub\Exceptions\PaymentError;
use StarfolkSoftware\Paysub\Models\Subscription;

trait ManagesPayment
{
    /**
     * Make a payment on invoice
     * @param Subscription|null $subscription
     * @return
     * @throws PaymentError
     */
    public function makePayment(Subscription $subscription)
    {
        $invoice = $subscription->openInvoice();

        if (! $invoice) {
            throw PaymentError::invoiceIsNull();
        }

        if (! $this->paystack_auth) {
            throw PaymentError::paystackAuthCodeIsNull();
        }

        if (! $this->paystackEmail()) {
            throw PaymentError::paystackEmailNotDefined();
        }

        $response = $this->charge($invoice->amount, $this->paystackEmail(), $this->paystack_auth->authorization_code);

        if ($response->status) {
            event(new InvoicePaid($invoice));
        }

        throw PaymentError::default($response->message);
    }
}
