<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Concerns\{
    ManagesCustomer,
    ManagesSubscriptions,
};

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscriptions;
}
