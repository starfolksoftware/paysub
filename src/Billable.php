<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Concerns\ManagesCustomer;
use StarfolkSoftware\PaystackSubscription\Concerns\ManagesSubscriptions;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscriptions;
}
