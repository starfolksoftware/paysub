<?php

namespace StarfolkSoftware\Paysub;

use Illuminate\Support\Facades\Facade;

/**
 * @see \StarfolkSoftware\Paysub\Subscription
 */
class PaysubFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paysub';
    }
}
