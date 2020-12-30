<?php

namespace StarfolkSoftware\PaystackSubscription;

use StarfolkSoftware\PaystackSubscription\Concerns\{
    ManagesCustomer,
    ManagesSubscription,
};

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscription;
}
