<?php

namespace StarfolkSoftware\Paysub;

use Carbon\Carbon;
use StarfolkSoftware\Paysub\Models\Invoice;
use StarfolkSoftware\Paysub\Models\Subscription;

class InvoiceBuilder
{
    /** @var array */
    protected $line_items = [];

    /** @var Subscription */
    protected $subscription;

    /** @var Plan */
    protected $plan;

    /**
     * Create a new invoice builder instance.
     *
     * @param  Subscription  $subscription
     * @return void
     */
    public function __construct(Subscription $subscription, $autofill_line_items = true)
    {
        $this->subscription($subscription, $autofill_line_items);
    }

    /**
     * Set a subscription on the invoice builder.
     *
     * @param  Subscription  $subscription
     * @param bool $autofill_line_items
     * @return $this
     */
    public function subscription(Subscription $subscription, $autofill = true)
    {
        $this->subscription = $subscription;
        $this->plan = $this->subscription->plan;

        if ($autofill) {
            $this->lineItem(
                trans('paysub::invoice.subscription_invoice'),
                $this->plan->amount,
                $this->subscription->quantity,
                $this->subscription->last_due_date,
                $this->subscription->next_due_date,
                $this->plan->tax_rates
            );

            $this->dueDate($subscription->next_due_date);
            $this->status(Invoice::STATUS_UNPAID);
            $this->description(trans('paysub::invoice.subscription_invoice'));
        }

        return $this;
    }

    /**
     * Add a description to the invoice builder
     *
     * @param string $description
     * @return $this
     */
    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the line items.
     *
     * @param  array  $line_items
     * @return $this
     */
    public function lineItems(array $line_items)
    {
        foreach ($line_items as $line_item) {
            $this->lineItem(
                $line_item['name'],
                $line_item['amount'],
                $line_item['quantity'],
                $line_item['start_date'],
                $line_item['end_date'],
                $line_item['tax_rates']
            );
        }

        return $this;
    }

    /**
     * add an item to the line items
     *
     * @param string $name
     * @param int $amount
     * @param int $quantity
     * @return $this
     */
    public function lineItem(
        string $name,
        int $amount,
        int $quantity,
        Carbon $start_date,
        Carbon $end_date,
        array $tax_rates,
        string $currency = 'NGN'
    ) {
        array_push($this->line_items, [
            'name' => $name,
            'amount' => $amount,
            'quantity' => $quantity,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tax_rates' => $tax_rates,
            'currency' => $currency,
        ]);

        return $this;
    }

    /**
     * Calculate the total payable
     *
     * @param float|null $amount
     * @return $this|float
     */
    protected function total($amount = null)
    {
        if ($amount) {
            $this->total = $amount;

            return $this;
        }

        $amount = collect($this->line_items)->reduce(function ($carry, $line_item) {
            $item = new InvoiceLineItem(null, (object) $line_item);

            $item_total = ((double) $line_item['amount'] * (int) $line_item['quantity']);

            if ($etp = $item->exclusiveTaxPercentage()) {
                $item_total = $item_total + ($item_total * $etp);
            }

            return $carry + $item_total;
        }, 0);

        return $amount;
    }

    /**
     * Set the tax.
     *
     * @param  array  $tax
     * @return $this
     */
    public function tax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Set the due date.
     *
     * @param  Carbon  $date
     * @return $this
     */
    public function dueDate($date)
    {
        $this->due_date = $date;

        return $this;
    }

    /**
     * Set the status.
     *
     * @param  string  $status
     * @return $this
     */
    public function status($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set the paid at.
     *
     * @param  Carbon  $paid_at
     * @return $this
     */
    public function paidAt($paid_at)
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    /**
     * Add a new invoice model.
     *
     * @return Invoice
     *
     */
    public function add()
    {
        return $this->create();
    }

    /**
     * Create a new invoice.
     *
     * @return Invoice
     */
    public function create()
    {
        /** @var Invoice $invoice */
        $invoice = $this->subscription->invoices()->create([
            'description' => $this->description ?? null,
            'line_items' => $this->line_items,
            'total' => $this->total(),
            'due_date' => $this->due_date,
            'status' => $this->status ?? Invoice::STATUS_UNPAID,
            'paid_at' => $this->paid_at ?? null,
        ]);

        $this->subscription->save();

        return $invoice;
    }
}
