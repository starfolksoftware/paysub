<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Exceptions\InvoiceCreationError;
use StarfolkSoftware\Paysub\InvoiceBuilder;
use StarfolkSoftware\Paysub\Models\Invoice;
use StarfolkSoftware\Paysub\Models\Subscription;

trait ManagesInvoice
{
    /**
     * Begin creating a new invoice.
     *
     * @param  Subscription  $subscription
     * @return InvoiceBuilder
     */
    public function newInvoice(Subscription $subscription, $autofill = true)
    {
        return new InvoiceBuilder($subscription, $autofill);
    }

    /**
     * Generate upcoming invoice
     *
     * @return Invoice
     * @throws InvoiceCreationError
     */
    public function generateUpcomingInvoice()
    {
        $subscription = $this->subscription();

        if ($subscription->hasOpenInvoice()) {
            throw InvoiceCreationError::hasOpenInvoice($this);
        }

        $invoice = $this->newInvoice(
            $subscription
        )->add();

        return $invoice;
    }

    /**
     * Create an invoice download Response.
     *
     * @param  string  $id
     * @param  array  $data
     * @param  string  $filename
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadInvoice($id, array $data, $filename = null)
    {
        $invoice = Invoice::findOrFail($id);

        return $filename ? $invoice->downloadAs($filename, $data) : $invoice->download($data);
    }

    /**
     * Get a collection of the entity's invoices.
     *
     * @param  bool  $includeVoid
     * @return \Illuminate\Support\Collection
     */
    public function invoices($includeVoid = false)
    {
        $builder = $this->subscription->invoices();

        if ($includeVoid) {
            $builder = $builder->paid()->unpaid();
        }

        return $builder->get();
    }

    /**
     * Get an array of the entity's invoices.
     *
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection
     */
    public function invoicesIncludingVoid()
    {
        return $this->invoices(true);
    }
}
