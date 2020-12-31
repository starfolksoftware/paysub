<?php

namespace StarfolkSoftware\Paysub\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Exceptions\InvoiceCreationError;

class Subscription extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'billing_cycle_anchor',
        'ends_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be appended
     */
    protected $appends = [
        'next_due_date'
    ];

    /**
     * Get the subscriber that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->owner();
    }

    /**
     * Get the model related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        $model = config('paysub.subscriber_model');

        return $this->belongsTo($model, 'subscriber_id');
    }

    /**
     * Get the plan of the subscription
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan() {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get the invoices of the subscription
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices() {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    /**
     * Get the payments of the subscription
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments() {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active();
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Filter query by active.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', 'active');
    }

    /**
     * Filter query by inactive.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInactive($query)
    {
        $query->where('status', 'inactive');
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return ! is_null($this->ends_at);
    }

    /**
     * Filter query by cancelled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeCancelled($query)
    {
        $query->whereNotNull('ends_at');
    }

    /**
     * Filter query by not cancelled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotCancelled($query)
    {
        $query->whereNull('ends_at');
    }

    /**
     * Determine if the subscription has ended.
     *
     * @return bool
     */
    public function hasEnded()
    {
        return $this->isCancelled();
    }

    /**
     * Filter query by ended.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEnded($query)
    {
        $query->cancelled();
    }

    /**
     * Check if subscription has open invoice
     */
    public function hasOpenInvoice() {
        $count = $this->invoices()->unpaid()->count();

        if ($count > 1) {
            throw InvoiceCreationError::multipleOpenInvoice($this);
        }

        return  $count === 1;
    }

    /**
     * Get the open invoice
     * 
     * @return Invoice|null
     */
    public function openInvoice() {
        if (! $this->hasOpenInvoice()) {
            return null;
        }

        return $this->invoices()->unpaid()->first();
    }

    /**
     * Increment the quantity of the subscription.
     *
     * @param  int  $count
     * @param  string|null  $plan
     * @return $this
     *
     */
    public function incrementQuantity($count = 1)
    {
        return $this->updateQuantity($this->quantity + $count);
    }

    /**
     * Decrement the quantity of the subscription.
     *
     * @param  int  $count
     * @param  string|null  $plan
     * @return $this
     *
     */
    public function decrementQuantity($count = 1)
    {
        return $this->updateQuantity(max(1, $this->quantity - $count));
    }

    /**
     * Update the quantity of the subscription.
     *
     * @param  int  $quantity
     * @param  string|null  $plan
     * @return $this
     *
     */
    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;

        $this->save();

        return $this;
    }

    /**
     * Calcuate the next payment date
     * 
     * @return \Carbon\Carbon
     */
    public function getNextPaymentDateAttribute() {
        $billingAnchor = Carbon::parse($this->billing_cycle_anchor);

        if ($this->plan->interval === Plan::INTERVAL_YEARLY) {
            return $billingAnchor->addYear();
        } else {
            $day = $billingAnchor->day;

            return Carbon::createFromDate(
                Carbon::now()->year,
                Carbon::now()->month,
                $day
            )->addMonth();
        }
    }

    /**
     * Cancel the subscription at the end of the billing period.
     *
     * @return $this
     */
    public function cancel()
    {
        $this->ends_at = $this->getNextPaymentDateAttribute();

        $this->status = self::STATUS_INACTIVE;

        $this->save();

        return $this;
    }

    /**
     * Resume the cancelled subscription.
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function resume()
    {
        $this->fill([
            'billing_cycle_anchor' => now(),
            'status' => self::STATUS_ACTIVE,
            'ends_at' => null,
        ])->save();

        return $this;
    }

    /**
     * Get the latest payment for a Subscription.
     *
     * @return Payment
     */
    public function latestPayment()
    {
        return $this->payments->latest();
    }
}
