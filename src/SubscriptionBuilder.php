<?php

namespace StarfolkSoftware\Paysub;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;
use StarfolkSoftware\Paysub\Concerns\Prorates;
use StarfolkSoftware\Paysub\Events\SubscriptionCreated;
use StarfolkSoftware\Paysub\Models\Plan;

class SubscriptionBuilder
{
    use Prorates;

    /**
     * The model that is subscribing.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The name of the subscription.
     *
     * @var string
     */
    protected $name;

    /**
     * The name of the plan being subscribed to.
     *
     * @var array
     */
    protected $items;

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
     * The coupon being applied to the subscription.
     *
     * @var string|null
     */
    protected $coupon;

    /**
     * The promotion code being applied to the subscription.
     *
     * @var string|null
     */
    protected $promotionCode;

    /**
     * Determines if user redeemable promotion codes are available in Stripe Checkout.
     *
     * @var bool
     */
    protected $allowPromotionCodes = false;

    /**
     * Create a new subscription builder instance.
     *
     * @param  mixed  $owner
     * @param  string $name
     * @param  Plan|Plan[]  $plan
     * @return void
     */
    public function __construct($owner, $name, Plan $plans = null)
    {
        $this->owner = $owner;
        $this->name = $name;

        foreach ((array) $plans as $plan) {
            $this->plan($plan);
        }
    }

    /**
     * Set a plan on the subscription builder.
     *
     * @param  Plan  $plan
     * @param  int  $quantity
     * @return $this
     */
    public function plan(Plan $plan, $quantity = 1)
    {
        $this->plan_id = $plan->id;
        $this->quantity = $quantity;
        
        $this->items[$plan->id] = $plan;

        return $this;
    }

    /**
     * Specify the quantity of a subscription item.
     *
     * @param  int  $quantity
     * @param Plan|null $plan
     * @return $this
     * 
     * @throws \InvalidArgumentException
     */
    public function quantity($quantity, Plan $plan = null) {
        if (is_null($plan)) {
            if (count($this->items) > 1) {
                throw new \InvalidArgumentException('Plan is required when creating multi-plan subscriptions.');
            }

            $plan = Arr::first($this->items);
        }

        return $this->plan($plan, $quantity);
    }

    /**
     * Specify the number of days of the trial.
     *
     * @param  int  $trialDays
     * @return $this
     */
    public function trialDays($trialDays) {
        $this->trialExpires = Carbon::now()->addDays($trialDays);

        return $this;
    }

    /**
     * Specify the ending date of the trial.
     *
     * @param  \Carbon\Carbon|\Carbon\CarbonInterface  $trialUntil
     * @return $this
     */
    public function trialUntil($trialUntil) {
        $this->trialExpires = $trialUntil;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->skipTrial = true;

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

    // /**
    //  * The coupon to apply to a new subscription.
    //  *
    //  * @param  string  $coupon
    //  * @return $this
    //  */
    // public function withCoupon($coupon)
    // {
    //     $this->coupon = $coupon;

    //     return $this;
    // }

    // /**
    //  * The promotion code to apply to a new subscription.
    //  *
    //  * @param  string  $promotionCode
    //  * @return $this
    //  */
    // public function withPromotionCode($promotionCode)
    // {
    //     $this->promotionCode = $promotionCode;

    //     return $this;
    // }

    // /**
    //  * Enables user redeemable promotion codes.
    //  *
    //  * @return $this
    //  */
    // public function allowPromotionCodes()
    // {
    //     $this->allowPromotionCodes = true;

    //     return $this;
    // }

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
     * 
     * @throws \Exception
     */
    public function create() {
        if (empty($this->items)) {
            throw new \Exception('At least one plan is required when starting subscriptions.');
        }

        /** @var \StarfolkSoftware\Paysub\Models\SubscriptionItem $firstItem */
        $firstItem = collect($this->items)->first();
        $isSinglePlan = collect($this->items)->count() === 1;

        /** @var \StarfolkSoftware\Paysub\Models\Subscription $subscription */
        $subscription = $this->owner->subscriptions()->create([
            'name' => $this->name,
            'plan_id' => $isSinglePlan ? $firstItem->plan->id : null,
            'quantity' => $this->quantity,
            'billing_cycle_anchor' => $this->billing_cycle_anchor,
            'trial_ends_at' => ! $this->skipTrial ? $this->trialExpires : null,
            'ends_at' => null,
        ]);

        /** @var \StarfolkSoftware\Paysub\Models\SubscriptionItem $item */
        foreach ($this->items as $key => $item) {
            $subscription->items()->create([
                'plan_id' => $key,
                'quantity' => $item->quantity,
            ]);
        }

        if (config('paysub.auto_invoice')) {
            event(new SubscriptionCreated($subscription));
        }

        return $subscription;
    }
}
