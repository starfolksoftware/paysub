<?php

namespace StarfolkSoftware\Paysub\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use StarfolkSoftware\Paysub\Events\SubscriptionCancelled;
use StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure;
use StarfolkSoftware\Paysub\Paysub;

class Subscription extends Model
{
    use HasFactory;
    
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_UNPAID = 'unpaid';

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
        'trial_ends_at',
    ];

    /**
     * The attributes that should be appended
     */
    protected $appends = [
        'next_due_date',
        'last_due_date',
    ];

    public function getTable()
    {
        return config('paysub.subscription_table_name', parent::getTable());
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
     * Get the items of the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(SubscriptionItem::class, 'subscription_id');
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
     * Determine if the subscription has multiple plans.
     *
     * @return bool
     */
    public function hasMultiplePlans()
    {
        return is_null($this->plan_id);
    }

    /**
     * Determine if the subscription has a single plan.
     *
     * @return bool
     */
    public function hasSinglePlan()
    {
        return ! $this->hasMultiplePlans();
    }

    /**
     * Determine if the subscription has a specific plan.
     *
     * @param  Plan  $plan
     * @return bool
     */
    public function hasPlan(Plan $plan)
    {
        if ($this->hasMultiplePlans()) {
            return $this->items->contains(function (SubscriptionItem $item) use ($plan) {
                return $item->plan_id === $plan->id;
            });
        }

        return (int) $this->plan_id === (int) $plan->id;
    }

    /**
     * Get the subscription item for the given plan.
     *
     * @param  Plan  $plan
     * @return \StarfolkSoftware\Paysub\Models\SubscriptionItem
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findItemOrFail(Plan $plan)
    {
        return $this->items()->where('plan_id', $plan->id)->firstOrFail();
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
        })
        ->where('status', '!=', self::STATUS_UNPAID)
        ->Where('status', '!=', self::STATUS_INACTIVE);

        if (Paysub::$deactivatePastDue) {
            $query->where('status', '!=', self::STATUS_PAST_DUE);
        }
    }

    /**
     * Determine if the subscription is recurring and not on trial.
     *
     * @return bool
     */
    public function recurring()
    {
        return ! $this->onTrial() && ! $this->cancelled();
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
     *
     * @return Invoice|null
     */
    public function hasOpenInvoice($withVoid = false)
    {
        if ($withVoid) {
            $builder = $this->invoices()->voidOrUnpaid();
        } else {
            $builder = $this->invoices()->unpaid();
        }

        $count = $builder->count();

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
     * Make sure a plan argument is provided when the subscription is a multi plan subscription.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function guardAgainstMultiplePlans()
    {
        if ($this->hasMultiplePlans()) {
            throw new InvalidArgumentException(
                'This method requires a plan argument since the subscription has multiple plans.'
            );
        }
    }

    /**
     * Increment the quantity of the subscription.
     *
     * @param  int  $count
     * @param  Plan|null  $plan
     * @return $this
     *
     */
    public function incrementQuantity($count = 1, Plan $plan = null)
    {
        if ($plan) {
            $this
                ->findItemOrFail($plan)
                ->incrementQuantity($count);

            return $this->refresh();
        }

        $this->guardAgainstMultiplePlans();

        return $this->updateQuantity($this->quantity + $count, $plan);
    }

    /**
     * Decrement the quantity of the subscription.
     *
     * @param  int  $count
     * @param  Plan|null  $plan
     * @return $this
     *
     */
    public function decrementQuantity($count = 1, Plan $plan = null)
    {
        if ($plan) {
            $this
                ->findItemOrFail($plan)
                ->decrementQuantity($count);

            return $this->refresh();
        }

        $this->guardAgainstMultiplePlans();

        return $this->updateQuantity(max(1, $this->quantity - $count), $plan);
    }

    /**
     * Update the quantity of the subscription.
     *
     * @param  int  $quantity
     * @param  Plan|null  $plan
     * @return $this
     * @throws SubscriptionUpdateFailure
     */
    public function updateQuantity($quantity, Plan $plan = null)
    {
        if ($plan) {
            $this
                ->findItemOrFail($plan)
                ->updateQuantity($quantity);

            return $this->refresh();
        }

        $this->guardAgainstMultiplePlans();

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
     * Skip trial.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function endTrial()
    {
        if (is_null($this->trial_ends_at)) {
            return $this;
        }

        $this->trial_ends_at = null;

        $this->save();

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

        $this->trial_ends_at = $date;

        $this->save();

        return $this;
    }

    /**
     * Swap the subscription to new plans.
     *
     * @param Plan|Plan[] $plans
     * @param array $quantities
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function swap($plans, $quantities = [])
    {
        if (empty((array) $plans)) {
            throw new \InvalidArgumentException('Please provide at least one plan when swapping.');
        }

        /** @var \StarfolkSoftware\Paysub\Models\SubscriptionItem $firstItem */
        $firstItem = collect($this->items)->first();
        $isSinglePlan = (collect($this->items)->count() === 1 && ! is_array($plans));

        $this->fill([
            'plan_id' => $isSinglePlan ? $plans->id : null,
            'quantity' => ($isSinglePlan) ?
                (key_exists($plans->name, $quantities) ? $quantities[$plans->name] : $firstItem->quantity) :
                null,
            'ends_at' => null,
        ])->save();

        if (! is_array($plans)) {
            $plans = [$plans];
        }

        foreach ($plans as $plan) {
            $this->items()->updateOrCreate([
                'plan_id' => $plan->id,
                'subscription_id' => $this->id,
            ], [
                'quantity' => $quantities[$plan->name] ?? $this->quantity,
            ]);
        }

        // Delete items that aren't attached to the subscription anymore...
        $this->items()->whereNotIn(
            'plan_id',
            collect($plans)->pluck('id')->filter()
        )->delete();

        $this->unsetRelation('items');

        return $this;
    }

    /**
     * Calcuate the next payment date
     *
     * @return \Carbon\Carbon|null
     */
    public function getNextDueDateAttribute()
    {
        // first invoice is about to be generated
        if ($this->invoices()->count() === 0) {
            return \Carbon\Carbon::parse($this->owner->trial_ends_at);
        }

        if ($this->hasMultiplePlans()) {
            $firstItem = $this->items()->first();
            list($interval_type, $interval_count) = array_values(Plan::find($firstItem->plan_id)->only('interval_type', 'interval_count'));
        } else {
            $interval_type = $this->plan->interval_type;
            $interval_count = $this->plan->interval_count;
        }

        switch ($interval_type) {
            case Plan::INTERVAL_DAILY:
                $date = Carbon::createFromDate(
                    null,
                    null,
                    $this->billing_cycle_anchor->day
                )->addDays($interval_count);

                break;
            
            case Plan::INTERVAL_WEEKLY:
                $date = Carbon::createFromDate(
                    null,
                    null,
                    $this->billing_cycle_anchor->day
                )->addWeeks($interval_count);

                break;
            
            case Plan::INTERVAL_MONTHLY:
                $date = Carbon::createFromDate(
                    null,
                    $this->billing_cycle_anchor->month,
                    $this->billing_cycle_anchor->day
                )->addMonths($interval_count);

                break;
            
            case Plan::INTERVAL_YEARLY:
                $date = Carbon::createFromDate(
                    $this->billing_cycle_anchor->year,
                    $this->billing_cycle_anchor->month,
                    $this->billing_cycle_anchor->day
                )->addYears($interval_count);

                break;
            
            default:
                $date = null;

                break;
        }

        return $date;
    }

    /**
     * Calcuate the last payment date
     *
     * @return \Carbon\Carbon|null
     */
    public function getLastDueDateAttribute()
    {
        // first invoice is about to be generated
        if ($this->invoices()->count() === 0) {
            return \Carbon\Carbon::parse($this->anchor_billing_cycle);
        }

        if ($this->hasMultiplePlans()) {
            $firstItem = $this->items()->first();
            list($interval_type, $interval_count) = array_values(Plan::find($firstItem->plan_id)->only('interval_type', 'interval_count'));
        } else {
            $interval_type = $this->plan->interval_type;
            $interval_count = $this->plan->interval_count;
        }

        switch ($interval_type) {
            case Plan::INTERVAL_DAILY:
                $date = Carbon::createFromDate(
                    null,
                    null,
                    $this->billing_cycle_anchor->day
                )->subDays($interval_count);

                break;
            
            case Plan::INTERVAL_WEEKLY:
                $date = Carbon::createFromDate(
                    null,
                    null,
                    $this->billing_cycle_anchor->day
                )->subWeeks($interval_count);

                break;
            
            case Plan::INTERVAL_MONTHLY:
                $date = Carbon::createFromDate(
                    null,
                    $this->billing_cycle_anchor->month,
                    $this->billing_cycle_anchor->day
                )->subMonths($interval_count);

                break;
            
            case Plan::INTERVAL_YEARLY:
                $date = Carbon::createFromDate(
                    $this->billing_cycle_anchor->year,
                    $this->billing_cycle_anchor->month,
                    $this->billing_cycle_anchor->day
                )->subYears($interval_count);

                break;
            
            default:
                $date = null;

                break;
        }

        return $date;
    }

    /**
     * Add a new plan to the subscription.
     *
     * @param  Plan  $plan
     * @param  int|null  $quantity
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function addPlan(Plan $plan, $quantity = 1)
    {
        if ($this->items->contains('plan_id', $plan->id)) {
            throw SubscriptionUpdateFailure::duplicatePlan($this, $plan);
        }

        $this->items()->create([
            'plan_id' => $plan->id,
            'quantity' => $quantity,
        ]);

        $this->unsetRelation('items');

        if ($this->hasSinglePlan()) {
            $this->fill([
                'plan_id' => null,
                'quantity' => null,
            ])->save();
        }

        return $this;
    }

    /**
     * Remove a plan from the subscription.
     *
     * @param  Plan  $plan
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function removePlan($plan)
    {
        if ($this->hasSinglePlan()) {
            throw SubscriptionUpdateFailure::cannotDeleteLastPlan($this);
        }

        $this->items()->where('plan_id', $plan->id)->delete();

        $this->unsetRelation('items');

        if ($this->items()->count() < 2) {
            $item = $this->items()->first();

            $this->fill([
                'plan_id' => $item->plan_id,
                'quantity' => $item->quantity,
            ])->save();
        }

        return $this;
    }

    /**
     * Cancel the subscription at the end of the billing period.
     *
     * @return $this
     */
    public function cancel()
    {
        // If the user was on trial, we will set the grace period to end when the trial
        // would have ended. Otherwise, we'll retrieve the end of the billing period
        // period and make that the end of the grace period for this current user.
        if ($this->onTrial()) {
            $this->ends_at = $this->trial_ends_at;
        } else {
            $this->ends_at = $this->next_due_date;
        }

        $this->save();

        event(new SubscriptionCancelled($this));

        return $this;
    }

    /**
     * Cancel the subscription at a specific moment in time.
     *
     * @param  \DateTimeInterface|int  $endsAt
     * @return $this
     */
    public function cancelAt($endsAt)
    {
        if ($endsAt instanceof DateTimeInterface) {
            $endsAt = $endsAt->getTimestamp();
        }
        
        $this->ends_at = Carbon::createFromTimestamp($endsAt);

        $this->save();

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
        if (! $this->onGracePeriod()) {
            throw new \LogicException('Unable to resume subscription that is not within grace period.');
        }

        // Finally, we will remove the ending timestamp from the user's record in the
        // local database to indicate that the subscription is active again and is
        // no longer "cancelled". Then we will save this record in the database.
        $this->fill([
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
     * Sync latest invoice
     *
     * @return Invoice
     */
    public function syncLatestInvoice()
    {
        $invoice = $this->latestInvoice();
        $line_items = [];

        foreach ($this->items as $key => $item) {
            array_push($line_items, [
                'name' => trans('paysub::invoice.subscription_invoice'),
                'amount' => $item->plan->amount,
                'quantity' => $item->quantity,
                'start_date' => $this->last_due_date,
                'end_date' => $this->next_due_date,
                'tax_rates' => $item->plan->tax_rates ?? [],
                'currency' => $item->plan->currency_code,
            ]);
        }

        $invoice->line_items = $line_items;
        $invoice->due_date = $this->next_due_date;
        $invoice
            ->calcTotal()
            ->save();

        return $invoice->refresh();
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
