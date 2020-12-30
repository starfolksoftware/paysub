<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Concerns\ManagesCustomer;
use StarfolkSoftware\PaystackSubscription\Concerns\ManagesSubscription;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscription;
}
