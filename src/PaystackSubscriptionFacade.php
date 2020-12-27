<?php

namespace Starfolksoftware\PaystackSubscription;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Starfolksoftware\PaystackSubscription\Subscription
 */
class PaystackSubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paystack-subscription';
    }
}
