<?php

namespace StarfolkSoftware\Paysub;

use Carbon\Carbon;
use StarfolkSoftware\Paysub\Models\{
    Subscription,
    Invoice
};

class InvoiceBuilder
{
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
        $this->subscription_id = $subscription->id;

        if ($autofill) {
            $this->lineItem(
                trans('paysub::invoice.invoice_bill_payment_name', [
                    'interval' => trans('paysub::invoice.'.$subscription->interval),
                    'from' => ($subscription->interval === Subscription::INTERVAL_MONTHLY) ? 
                        $subscription->next_due_date->subMonth() : $subscription->next_due_date->subYear(), 
                    'to' => $subscription->next_due_date
                ]),
                ($subscription->plan->amount * $subscription->quantity),
                $subscription->quantity
            );

            $this->dueDate($subscription->next_due_date);
            $this->status(Invoice::STATUS_UNPAID);
        }

        return $this;
    }

    /**
     * Add a description to the invoice builder
     * 
     * @param string $description
     * @return $this
     */
    public function description(string $description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the line items.
     *
     * @param  array  $line_items
     * @return $this
     */
    public function lineItems(array $line_items) {
        foreach ($line_items as $line_item) {
            $this->lineItem(
                $line_item['name'],
                $line_item['amount'],
                $line_item['quantity']
            );
        }

        return $this;
    }

    /**
     * add an item to the line items
     * 
     * @param string $name
     * @param integer $amount
     * @param integer $quantity
     * @return $this
     */
    public function lineItem($name, $amount, $quantity) {
        if (! $this->line_items) {
            $this->line_items = array();
        }

        array_push($this->line_items, array(
            'name' => $name,
            'amount' => $amount,
            'quantity' => $quantity,
        ));

        return $this;
    }

    /**
     * Calculate the amount payable
     * 
     * @param double|null $amount
     * @return $this|double
     */
    public function amount($amount = null) {
        if ($amount) {
            $this->amount = $amount;
            return $this;
        }

        $amount = collect($this->line_items)->reduce(function ($carry, $line_item) {
            return $carry + (double) $line_item['amount'];
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
            'tax' => $this->tax ?? null,
            'amount' => $this->amount(),
            'due_date' => $this->due_date,
            'status' => $this->status,
            'paid_at' => $this->paid_at ?? null
        ]);

        $this->subscription->save();

        return $invoice;
    }
}
