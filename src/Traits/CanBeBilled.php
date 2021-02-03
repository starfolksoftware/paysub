<?php

namespace StarfolkSoftware\Paysub\Traits;

use StarfolkSoftware\Paysub\Concerns\HasPaystackTransactionApi;
use StarfolkSoftware\Paysub\Concerns\ManagesAuthorization;
use StarfolkSoftware\Paysub\Concerns\ManagesInvoice;
use StarfolkSoftware\Paysub\Concerns\ManagesPayment;
use StarfolkSoftware\Paysub\Concerns\ManagesSubscription;

trait CanBeBilled
{
    use HasPaystackTransactionApi;
    use ManagesInvoice;
    use ManagesPayment;
    use ManagesSubscription;
    use ManagesAuthorization;
}
