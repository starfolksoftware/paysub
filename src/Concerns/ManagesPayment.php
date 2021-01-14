<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Events\InvoicePaid;
use StarfolkSoftware\Paysub\Exceptions\PaymentError;

trait ManagesPayment
{
    /**
     * Make a payment on invoice
     *
     * @throws PaymentError
     */
    public function makePayment()
    {
        $invoice = $this->subscription()->openInvoice();

        if (! $invoice) {
            throw PaymentError::invoiceIsNull();
        }

        if (! $this->paystack_auth) {
            throw PaymentError::paystackAuthCodeIsNull();
        }

        if (! $this->paystackEmail()) {
            throw PaymentError::paystackEmailNotDefined();
        }

        $response = $this->chargeUsingPaystack($invoice->amount, $this->paystackEmail(), $this->paystack_auth->authorization_code);

        if ($response->status) {
            event(new InvoicePaid($invoice));
        }

        throw PaymentError::default($response->message);
    }
}
