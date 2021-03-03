<?php

namespace StarfolkSoftware\Paysub\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use StarfolkSoftware\Paysub\Events\SubscriptionCancelled;
use StarfolkSoftware\Paysub\Exceptions\InvoiceCreationError;
use StarfolkSoftware\Paysub\Paysub;

class Subscription extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_UNPAID = 'unpaid';
    const INTERVAL_MONTHLY = 'monthly';
    const INTERVAL_YEARLY = 'yearly';

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
        'next_due_date',
    ];

    public function getTable()
    {
        return config('paysub.subscription_table_name', parent::getTable());
    }

    public function getBillingCycleAnchorAttribute($value)
    {
        $date = Carbon::parse($value);

        if ($this->interval === self::INTERVAL_YEARLY) {
            return [
                'interval' => self::INTERVAL_YEARLY,
                'day' => $date->day,
                'month' => $date->month,
            ];
        } elseif ($this->interval === self::INTERVAL_MONTHLY) {
            return [
                'interval' => self::INTERVAL_MONTHLY,
                'day' => $date->day,
                'month' => null,
            ];
        }

        return null;
    }

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
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get the invoices of the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    /**
     * Get the payments of the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'subscription_id',
            'invoice_id',
            'id',
            'id'
        );
    }

    /**
     * Determine if the subscription has a specific plan.
     *
     * @param  Plan  $plan
     * @return bool
     */
    public function hasPlan(Plan $plan)
    {
        return $this->plan->id === $plan->id;
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is past due.
     *
     * @return bool
     */
    public function pastDue()
    {
        return $this->status === self::STATUS_PAST_DUE;
    }

    /**
     * Filter query by past due.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePastDue($query)
    {
        $query->where('status', self::STATUS_PAST_DUE);
    }

    /**
     * Determine if the subscription is active.
     *
     * @return bool
     */
    public function active()
    {
        return (is_null($this->ends_at) || $this->onGracePeriod()) &&
            (! Paysub::$deactivatePastDue || $this->status !== self::STATUS_PAST_DUE) &&
            $this->status !== self::STATUS_UNPAID;
    }

    /**
     * Filter query by active.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where(function ($query) {
            $query->whereNull('ends_at')
                ->orWhere(function ($query) {
                    $query->onGracePeriod();
                });
        })->where('status', '!=', self::STATUS_UNPAID);

        if (Paysub::$deactivatePastDue) {
            $query->where('status', '!=', self::STATUS_PAST_DUE);
        }
    }

    /**
     * Filter query by recurring.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeRecurring($query)
    {
        $query->notOnTrial()->notCancelled();
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function cancelled()
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
    public function ended()
    {
        return $this->cancelled() && ! $this->onGracePeriod();
    }

    /**
     * Filter query by ended.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEnded($query)
    {
        $query->cancelled()->notOnGracePeriod();
    }

    /**
     * Determine if the subscription is within its trial period.
     *
     * @return bool
     */
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Filter query by on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnTrial($query)
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnTrial($query)
    {
        $query->whereNull('trial_ends_at')->orWhere('trial_ends_at', '<=', Carbon::now());
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod()
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Filter query by on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnGracePeriod($query)
    {
        $query->whereNotNull('ends_at')->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnGracePeriod($query)
    {
        $query->whereNull('ends_at')->orWhere('ends_at', '<=', Carbon::now());
    }

    /**
     * Check if subscription has open invoice
     */
    public function hasOpenInvoice($withVoid = false)
    {
        if ($withVoid) {
            $builder = $this->invoices()->voidOrUnpaid();
        } else {
            $builder = $this->invoices()->unpaid();
        }

        $count = $builder->count();

        if ($count > 1) {
            throw InvoiceCreationError::multipleOpenInvoice($this);
        }

        return  $builder->latest()->first();
    }

    /**
     * Get the open invoice
     *
     * @return Invoice|null
     */
    public function openInvoice($withVoid = false)
    {
        if (! $invoice = $this->hasOpenInvoice($withVoid)) {
            return null;
        }

        return $invoice;
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
     * Change the billing cycle anchor on a plan change.
     *
     * @param  \DateTimeInterface|int|string  $date
     * @return $this
     */
    public function anchorBillingCycleOn($date = 'now')
    {
        if ($date instanceof DateTimeInterface) {
            $date = $date->getTimestamp();
        }

        $this->billing_cycle_anchor = $date;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * This method must be combined with swap, resume, etc.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Extend an existing subscription's trial period.
     *
     * @param  \Carbon\CarbonInterface  $date
     * @return $this
     */
    public function extendTrial(CarbonInterface $date)
    {
        if (! $date->isFuture()) {
            throw new InvalidArgumentException("Extending a subscription's trial requires a date in the future.");
        }

        $subscription = $this->asStripeSubscription();

        $subscription->trial_end = $date->getTimestamp();

        $subscription->save();

        $this->trial_ends_at = $date;

        $this->save();

        return $this;
    }

    /**
     * Swap the subscription to new Stripe plans.
     *
     * @param  Play  $plan
     * @param  string  $interval
     * @return $this
     *
     */
    public function swap(Plan $plan, string $interval = self::INTERVAL_MONTHLY)
    {
        $this->fill([
            'plan_id' => $plan->id,
            'interval' => $interval,
            'created_at' => now(),
        ])->save();

        return $this;
    }

    /**
     * Calcuate the next payment date
     *
     * @return \Carbon\Carbon|null
     */
    public function getNextDueDateAttribute()
    {
        $anchor = $this->billing_cycle_anchor;
        $date = Carbon::createFromDate(null, $anchor['month'], $anchor['day']);

        return $anchor['month'] ? $date->addYear() : $date->addMonth();
    }

    /**
     * Cancel the subscription at the end of the billing period.
     *
     * @return $this
     */
    public function cancel()
    {
        $this->ends_at = $this->next_due_date;

        $this->save();

        event(new SubscriptionCancelled($this));

        return $this;
    }

    /**
     * Cancel the subscription immediately.
     *
     * @return $this
     */
    public function cancelNow()
    {
        $this->markAsCancelled();

        return $this;
    }

    /**
     * Mark the subscription as cancelled.
     *
     * @return void
     * @internal
     */
    public function markAsCancelled()
    {
        $this->fill([
            'status' => self::STATUS_INACTIVE,
            'ends_at' => Carbon::now(),
        ])->save();

        event(new SubscriptionCancelled($this));
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
     * Get the latest invoice for the subscription.
     *
     * @return Invoice
     */
    public function latestInvoice()
    {
        return $this->invoices()->latest()->first();
    }

    /**
     * Get the latest payment for a Subscription.
     *
     * @return Payment
     */
    public function latestPayment()
    {
        return $this->payments()->latest()->first();
    }
}
