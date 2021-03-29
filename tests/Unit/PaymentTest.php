<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Payment;
use StarfolkSoftware\Paysub\Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $payment = new Payment;

        $this->assertEquals($payment->getTable(), config(
            'paysub.payment_table_name'
        ));
    }
}
