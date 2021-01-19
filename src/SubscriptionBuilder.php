<?php

namespace StarfolkSoftware\Paysub;

use Carbon\Carbon;
use DateTimeInterface;
use StarfolkSoftware\Paysub\Events\SubscriptionCreated;
use StarfolkSoftware\Paysub\Models\{
    Plan
};

class SubscriptionBuilder
{
    /**
     * The model that is subscribing.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The date and time the trial will expire.
     *
     * @var \Carbon\Carbon|\Carbon\CarbonInterface
     */
    protected $trialExpires;

    /**
     * Indicates that the trial should end immediately.
     *
     * @var bool
     */
    protected $skipTrial = false;

    /**
     * The date on which the billing cycle should be anchored.
     *
     * @var int|null
     */
    protected $billingCycleAnchor = null;

    /**
     * Create a new subscription builder instance.
     *
     * @param  mixed  $owner
     * @param  Plan  $plan
     * @return void
     */
    public function __construct($owner, Plan $plan, $interval = null)
    {
        $this->owner = $owner;
        $this->subscriber_id = $this->owner->id;

        $this->plan($plan, 1, $interval);
    }

    /**
     * Set a plan on the subscription builder.
     *
     * @param  Plan  $plan
     * @param  int  $quantity
     * @param  string|null $interval
     * @return $this
     */
    public function plan(Plan $plan, $quantity = 1, $interval = null)
    {
        $this->plan_id = $plan->id;
        $this->quantity = $quantity;
        
        if ($interval) {
            $this->interval = $interval;
        }

        return $this;
    }

    /**
     * Specify the quantity of a subscription item.
     *
     * @param  int  $quantity
     * @return $this
     */
    public function quantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Specify the number of days of the trial.
     *
     * @param  int  $trialDays
     * @return $this
     */
    public function trialDays($trialDays)
    {
        $this->owner->trial_ends_at = Carbon::now()->addDays($trialDays);

        return $this;
    }

    /**
     * Specify the ending date of the trial.
     *
     * @param  \Carbon\Carbon|\Carbon\CarbonInterface  $trialUntil
     * @return $this
     */
    public function trialUntil($trialUntil)
    {
        $this->owner->trial_ends_at = $trialUntil;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->owner->trial_ends_at = now();

        return $this;
    }

    /**
     * Change the billing cycle anchor on a plan creation.
     *
     * @param  \DateTimeInterface|int  $date
     * @return $this
     */
    public function anchorBillingCycleOn($date)
    {
        if ($date instanceof DateTimeInterface) {
            $date = $date->getTimestamp();
        }

        $this->billing_cycle_anchor = $date;

        return $this;
    }

    /**
     * Add a new subscription to the model.
     *
     * @return \StarfolkSoftware\Paysub\Models\Subscription
     *
     */
    public function add()
    {
        return $this->create();
    }

    /**
     * Create a new subscription.
     *
     * @return \StarfolkSoftware\Paysub\Models\Subscription
     */
    public function create()
    {
        /** @var \StarfolkSoftware\Paysub\Models\Subscription $subscription */
        $subscription = $this->owner->subscriptions()->create([
            'plan_id' => $this->plan_id,
            'quantity' => $this->quantity,
            'billing_cycle_anchor' => $this->billing_cycle_anchor,
        ]);

        $this->owner->save();

        event(new SubscriptionCreated($subscription));

        return $subscription;
    }
}
