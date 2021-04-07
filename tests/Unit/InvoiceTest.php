<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use Carbon\Carbon;
use StarfolkSoftware\Paysub\Models\Invoice;
use StarfolkSoftware\Paysub\Tests\TestCase;

class InvoiceTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $invoice = new Invoice;

        $this->assertEquals($invoice->getTable(), config(
            'paysub.invoice_table_name'
        ));
    }

    public function test_we_can_get_a_carbon_date_for_the_invoice()
    {
        $invoice = new Invoice([
            "created_at" => now(),
        ]);

        $this->assertInstanceOf(Carbon::class, $invoice->date());
    }
}
