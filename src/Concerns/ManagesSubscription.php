<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\{Subscription,Plan};
use StarfolkSoftware\Paysub\SubscriptionBuilder;

trait ManagesSubscription {
    /**
     * Begin creating a new subscription.
     *
     * @param  string  $name
     * @param  Plan  $plan
     * @return \StarfolkSoftware\Paysub\SubscriptionBuilder
     */
    public function newSubscription(Plan $plan)
    {
        return new SubscriptionBuilder($this, $plan);
    }

    /**
     * Determine if the model is on trial.
     *
     * @return bool
     */
    public function onTrial()
    {
        if ($this->onGenericTrial()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the model is on a "generic" trial at the model level.
     *
     * @return bool
     */
    public function onGenericTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
