<?php

namespace StarfolkSoftware\Paysub;

use Carbon\Carbon;

class InvoiceLineItem {
    /**
     * The Invoice instance.
     *
     * @var \Laravel\Cashier\Invoice
     */
    protected $invoice;

    /**
     * The invoice line item instance.
     *
     * @var \StarfolkSoftware\Paysub\InvoiceLineItem
     */
    protected $item;

    /**
     * Create a new invoice line item instance.
     *
     * @param  \StarfolkSoftware\Paysub\Models\Invoice|null  $invoice
     * @param  \StarfolkSoftware\Paysub\InvoiceLineItem  $item
     * @return void
     */
    public function __construct($invoice, InvoiceLineItem $item)
    {
        $this->invoice = $invoice;
        $this->item = $item;
    }

    /**
     * Get the total for the invoice line item.
     *
     * @return string
     */
    public function total()
    {
        return $this->formatAmount($this->item->amount);
    }

    /**
     * Determine if the line item has both inclusive and exclusive tax.
     *
     * @return bool
     */
    public function hasBothInclusiveAndExclusiveTax()
    {
        return $this->inclusiveTaxPercentage() && $this->exclusiveTaxPercentage();
    }

    /**
     * Get the total percentage of the default inclusive tax for the invoice line item.
     *
     * @return int|null
     */
    public function inclusiveTaxPercentage()
    {
        return $this->calculateTaxPercentageByTaxRate(true);
    }

    /**
     * Get the total percentage of the default exclusive tax for the invoice line item.
     *
     * @return int
     */
    public function exclusiveTaxPercentage()
    {
        return $this->calculateTaxPercentageByTaxRate(false);
    }

    /**
     * Calculate the total tax percentage for either the inclusive or exclusive tax by tax rate.
     *
     * @param  bool  $inclusive
     * @return int
     */
    protected function calculateTaxPercentageByTaxRate($inclusive)
    {
        if (! $this->item->tax_rates) {
            return 0;
        }

        return (int) collect($this->item->tax_rates)
            ->filter(function ($taxRate) use ($inclusive) {
                return ((object) $taxRate)->inclusive === (bool) $inclusive;
            })
            ->sum(function ($taxRate) {
                return ((object) $taxRate)->percentage;
            });
    }

    /**
     * Determine if the invoice line item has tax rates.
     *
     * @return bool
     */
    public function hasTaxRates()
    {
        return ! empty($this->item->tax_rates);
    }

    /**
     * Get a Carbon instance for the start date.
     *
     * @return \Carbon\Carbon
     */
    public function startDateAsCarbon()
    {
        return $this->item->start_date;
    }

    /**
     * Get a Carbon instance for the end date.
     *
     * @return \Carbon\Carbon
     */
    public function endDateAsCarbon()
    {
        return $this->item->end_date;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @return string
     */
    protected function formatAmount($amount)
    {
        return Paysub::formatAmount($amount, $this->item->currency);
    }

    /**
     * Get the model instance.
     *
     * @return \StarfolkSoftware\Paysub\Models\Invoice
     */
    public function invoice()
    {
        return $this->invoice;
    }

    /**
     * Dynamically access the invoice line item instance.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->item->{$key};
    }
}
