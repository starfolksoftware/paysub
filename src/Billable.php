<?php

namespace StarfolkSoftware\Paysub;

use StarfolkSoftware\Paysub\Concerns\ManagesCustomer;
use StarfolkSoftware\Paysub\Concerns\ManagesSubscription;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscription;
}
