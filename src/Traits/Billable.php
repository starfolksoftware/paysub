<?php

namespace StarfolkSoftware\Paysub\Traits;

use StarfolkSoftware\Paysub\Concerns\HasTransactionApi;
use StarfolkSoftware\Paysub\Concerns\ManagesInvoice;
use StarfolkSoftware\Paysub\Concerns\ManagesPayment;
use StarfolkSoftware\Paysub\Concerns\ManagesSubscription;

trait Billable
{
    use HasTransactionApi;
    use ManagesInvoice;
    use ManagesPayment;
    use ManagesSubscription;
}
