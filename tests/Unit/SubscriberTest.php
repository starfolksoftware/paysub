<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use StarfolkSoftware\Paysub\Tests\Fixtures\User;

class SubscriberTest extends TestCase
{
    public function test_customer_can_be_put_on_a_generic_trial()
    {
        $subscriber = new User;

        $this->assertFalse($subscriber->onGenericTrial());

        $subscriber->trial_ends_at = $tomorrow = Carbon::tomorrow();

        $this->assertTrue($subscriber->onGenericTrial());

        $subscriber->trial_ends_at = Carbon::today()->subDays(5);

        $this->assertFalse($subscriber->onGenericTrial());
    }

    public function test_customer_returns_invoice_mailables()
    {
        $subscriber = new User;
        $mailables = $subscriber->invoiceMailables();

        $this->assertTrue(in_array('user@example.com', $mailables));
    }
}
