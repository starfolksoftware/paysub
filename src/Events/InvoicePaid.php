<?php

namespace StarfolkSoftware\Paysub\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use StarfolkSoftware\Paysub\Models\Invoice;

class InvoicePaid {
    use Dispatchable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice) {
        $this->invoice = $invoice;
    }
}
