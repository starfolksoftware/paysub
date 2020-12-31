<?php

namespace StarfolkSoftware\PaystackSubscription;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\PaystackSubscription\Actions\Subscription\Disable as PaystackSubscriptionDisable;
use StarfolkSoftware\PaystackSubscription\Actions\Subscription\Enable as PaystackSubscriptionEnable;
use StarfolkSoftware\PaystackSubscription\Actions\Subscription\Retrieve as PaystackSubscriptionRetrieve;
use StarfolkSoftware\PaystackSubscription\Exceptions\InvalidSubscription;

class Subscription extends Model
{
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
        'ends_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
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

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    /**
     * Determine if the subscription has a specific plan.
     *
     * @param  string  $plan
     * @return bool
     */
    public function hasPlan($plan)
    {
        return $this->paystack_plan === $plan;
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
    public function active()
    {
        return $this->paystack_status === 'active';
    }

    /**
     * Filter query by active.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('paystack_status', 'active');
    }

    /**
     * Sync the Paystack status of the subscription.
     *
     * @return void
     */
    public function syncPaystackStatus()
    {
        $subscription = $this->asPaystackSubscription();

        $this->paystack_status = $subscription->status;

        $this->save();
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
        return $this->cancelled();
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
        $paystackSubscription = $this->asPaystackSubscription();

        $paystackSubscription->quantity = $quantity;

        /**
         * This is where you update the paystack subscription.
         * Right now it doesnt seem to be supported.
         */

        $this->quantity = $quantity;

        $this->save();

        return $this;
    }

    /**
     * Cancel the subscription at the end of the billing period.
     *
     * @return $this
     */
    public function cancel()
    {
        $subscription = $this->asPaystackSubscription();

        /**
         * Cancel subscription
         */
        (new PaystackSubscriptionDisable())->execute(
            $subscription->subscription_code,
            $subscription->email_token
        );

        $subscription = $this->asPaystackSubscription();

        $this->paystack_status = $subscription->status;

        $this->ends_at = Carbon::createFromTimestamp(
            $subscription->next_payment_date
        );

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
        $subscription = $this->asPaystackSubscription();

        /**
         * Resume subscription
         */
        (new PaystackSubscriptionEnable())->execute(
            $subscription->subscription_code,
            $subscription->email_token
        );

        $subscription = $this->asPaystackSubscription();

        // Finally, we will remove the ending timestamp from the user's record in the
        // local database to indicate that the subscription is active again and is
        // no longer "cancelled". Then we will save this record in the database.
        $this->fill([
            'paystack_status' => $subscription->status,
            'ends_at' => null,
        ])->save();

        return $this;
    }

    /**
     * Get the latest invoice for the subscription.
     *
     * @return \StarfolkSoftware\PaystackSubscription\Invoice
     */
    public function openInvoice()
    {
        $paystackSubscription = $this->asPaystackSubscription();

        return $paystackSubscription->open_invoice;
    }

    /**
     * Get the latest payment for a Subscription.
     *
     * @return \StarfolkSoftware\PaystackSubscription\Payment|null
     */
    public function latestPayment()
    {
        $subscription = $this->asPaystackSubscription();

        return end($subscription);
    }

    /**
     * Determine if the entity has a Paystack customer code.
     *
     * @return bool
     */
    public function hasPaystackCode()
    {
        return ! is_null($this->paystack_code);
    }

    /**
     * Determine if the entity has a Paystack subscirption code and throw an exception if not.
     *
     * @return void
     *
     * @throws \StarfolkSoftware\PaystackSubscription\Exceptions\InvalidSubscription
     */
    protected function assertSubscriptionExists()
    {
        if (! $this->hasPaystackCode()) {
            throw InvalidSubscription::notYetCreated($this);
        }
    }

    /**
     * Get the subscription as a Paystack subscription object.
     *
     * @param  array  $expand
     * @return \StarfolkSoftware\PaystackSubscription\Api\Subscription
     */
    public function asPaystackSubscription()
    {
        $this->assertSubscriptionExists();

        return (new PaystackSubscriptionRetrieve())->execute($this->paystack_code);
    }
}
