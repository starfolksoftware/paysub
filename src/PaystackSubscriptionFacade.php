<?php

namespace StarfolkSoftware\PaystackSubscription;

use Illuminate\Support\Facades\Facade;

/**
 * @see \StarfolkSoftware\PaystackSubscription\Subscription
 */
class PaystackSubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paystack-subscription';
    }
}
