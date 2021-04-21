<?php

namespace StarfolkSoftware\Paysub\Exceptions;

use Exception;
use StarfolkSoftware\Paysub\Models\Plan;
use StarfolkSoftware\Paysub\Models\Subscription;

class SubscriptionUpdateFailure extends Exception
{
    final public function __construct()
    {
    }
    
    /**
     * Create a new InvoiceCreationError instance.
     *
     *
     * @return static
     */
    public static function default()
    {
        return new static('subscription update failed.');
    }

    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \StarfolkSoftware\Paysub\Models\Subscription  $subscription
     * @return static
     */
    public static function incompleteSubscription(Subscription $subscription)
    {
        return new static(
            "The subscription \"{$subscription->stripe_id}\" cannot be updated because its payment is incomplete."
        );
    }

    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \StarfolkSoftware\Paysub\Models\Subscription  $subscription
     * @param  \StarfolkSoftware\Paysub\Models\Plan  $plan
     * @return static
     */
    public static function duplicatePlan(Subscription $subscription, Plan $plan)
    {
        return new static(
            "The plan \"$plan\" is already attached to subscription \"{$subscription->stripe_id}\"."
        );
    }

    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \StarfolkSoftware\Paysub\Models\Subscription  $subscription
     * @return static
     */
    public static function cannotDeleteLastPlan(Subscription $subscription)
    {
        return new static(
            "The plan on subscription \"{$subscription->stripe_id}\" cannot be removed because it is the last one."
        );
    }
}
