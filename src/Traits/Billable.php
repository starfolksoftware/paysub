<?php

namespace StarfolkSoftware\Paysub\Traits;

use StarfolkSoftware\Paysub\Concerns\{
    HasTransactionApi,
    ManagesInvoice,
    ManagesPayment,
    ManagesSubscription
};

trait Billable {
    use HasTransactionApi;
    use ManagesInvoice;
    use ManagesPayment;
    use ManagesSubscription;
}
