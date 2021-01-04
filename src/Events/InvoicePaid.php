<?php

namespace StarfolkSoftware\Paysub\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use StarfolkSoftware\Paysub\Models\Invoice;

class InvoicePaid
{
    use Dispatchable;
    use SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
