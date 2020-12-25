<?php

namespace Starfolksoftware\Subscription;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Starfolksoftware\Subscription\Subscription
 */
class SubscriptionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paystack-subscription';
    }
}
