<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use InvalidArgumentException;
use StarfolkSoftware\Paysub\Models\Subscription;
use StarfolkSoftware\Paysub\Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_we_can_check_if_a_subscription_is_active()
    {
        $subscription = new Subscription([
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertTrue($subscription->active());
    }

    public function test_we_can_check_if_a_subscription_is_inactive()
    {
        $subscription = new Subscription([
            'status' => Subscription::STATUS_INACTIVE,
        ]);

        $this->assertTrue($subscription->status === 'inactive');
        $this->assertFalse($subscription->pastDue());
        $this->assertFalse($subscription->status === 'unpaid');
    }

    public function test_we_can_check_if_a_subscription_is_past_due()
    {
        $subscription = new Subscription([
            'status' => Subscription::STATUS_PAST_DUE,
        ]);

        $this->assertTrue($subscription->pastDue());
    }

    public function test_we_can_check_if_a_subscription_is_unpaid()
    {
        $subscription = new Subscription([
            'status' => Subscription::STATUS_UNPAID,
        ]);

        $this->assertFalse($subscription->status === 'inactive');
        $this->assertFalse($subscription->pastDue());
        $this->assertTrue($subscription->status === 'unpaid');
    }

    public function test_extending_a_trial_requires_a_date_in_the_future()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Subscription)->extendTrial(now()->subDay());
    }
}
