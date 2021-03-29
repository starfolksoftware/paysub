<?php

namespace StarfolkSoftware\Paysub\Tests\Feature;

use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;
use StarfolkSoftware\Paysub\Models\Plan;
use StarfolkSoftware\Paysub\Models\Subscription;
use StarfolkSoftware\Paysub\Paysub;
use StarfolkSoftware\Paysub\Tests\Fixtures\User;

class SubscriptionTest extends FeatureTestCase
{
    /**
     * @var Plan
     */
    protected static $basicPlan;

    /**
     * @var Plan
     */
    protected static $standardPlan;

    /**
     * @var Plan
     */
    protected static $professionalPlan;

    public function setUp(): void
    {
        parent::setUp();

        self::$basicPlan = Plan::create([
            'name' => 'Basic',
            'description' => '',
            'amount' => 250000,
        ]);

        self::$standardPlan = Plan::create([
            'name' => 'Standard',
            'description' => '',
            'amount' => 850000,
        ]);

        self::$professionalPlan = Plan::create([
            'name' => 'Professional',
            'description' => '',
            'amount' => 1050000,
        ]);
    }

    // public static function tearDownAfterClass(): void {
    //     parent::tearDownAfterClass();

    // }

    public function test_subscriptions_can_be_created()
    {
        $subscriber = $this->createCustomer();

        $subscriber->newSubscription(self::$basicPlan)
            ->monthly()
            ->quantity(1)
            ->trialDays(7)
            ->anchorBillingCycleOn(now()->addDays(7))
            ->add();

        $this->assertEquals(1, $subscriber->subscriptions()->count());
        $this->assertNotNull(($subscription = $subscriber->subscription())->id);

        $this->assertTrue($subscriber->subscribed(self::$basicPlan));
        $this->assertFalse($subscriber->subscribed(self::$standardPlan));
        $this->assertFalse($subscriber->subscribed(self::$professionalPlan));

        $this->assertTrue($subscription->active());
        $this->assertFalse($subscription->cancelled());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // cancel the subscription
        $subscription->cancel();

        $this->assertTrue($subscription->active());
        $this->assertTrue($subscription->cancelled());
        $this->assertTrue($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // Modify Ends Date To Past
        $oldGracePeriod = $subscription->ends_at;
        $subscription->fill(['ends_at' => Carbon::now()->subDays(5)])->save();

        $this->assertFalse($subscription->active());
        $this->assertTrue($subscription->cancelled());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertTrue($subscription->ended());

        $subscription->fill(['ends_at' => $oldGracePeriod])->save();

        // Resume Subscription
        $subscription->resume();

        $this->assertTrue($subscription->active());
        $this->assertFalse($subscription->cancelled());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // Increment & Decrement
        $subscription->incrementQuantity();

        $this->assertEquals(2, $subscription->quantity);

        $subscription->decrementQuantity();

        $this->assertEquals(1, $subscription->quantity);

        // Invoice Tests
        $invoice = $subscriber->invoices()[0];

        $this->assertEquals('250000', $invoice->amount);
        $this->assertInstanceOf(Carbon::class, $invoice->date());
    }

    public function test_swapping_subscription_and_preserving_quantity()
    {
        $subscriber = $this->createCustomer();
        $subscription = $subscriber->newSubscription(self::$basicPlan)
            ->quantity(5)
            ->anchorBillingCycleOn(now())
            ->add();

        $subscription = $subscription->swap(self::$standardPlan);

        $this->assertSame(5, $subscription->quantity);
    }

    public function test_swapping_subscription_and_adopting_new_quantity()
    {
        $subscriber = $this->createCustomer();
        $subscription = $subscriber->newSubscription(self::$basicPlan)
            ->quantity(5)
            ->anchorBillingCycleOn(now())
            ->add();

        $subscription = $subscription->swap(self::$standardPlan, null, 3);

        $this->assertSame(3, $subscription->quantity);
    }

    public function test_creating_subscription_with_an_anchored_billing_cycle()
    {
        $subscriber = $this->createCustomer();

        // Create Subscription
        $subscriber->newSubscription(self::$basicPlan)
            ->anchorBillingCycleOn(new DateTime('first day of next month'))
            ->add();

        $subscription = $subscriber->subscription();

        $this->assertTrue($subscriber->subscribed(self::$basicPlan));
        $this->assertFalse($subscriber->subscribed(self::$standardPlan));
        $this->assertTrue($subscription->active());
        $this->assertFalse($subscription->cancelled());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // Invoice Tests
        // $invoice = $subscriber->invoices()[0];
        // $invoicePeriod = $invoice->invoiceItems()[0]->period;

        // $this->assertEquals(
        //     (new DateTime('now'))->format('Y-m-d'),
        //     date('Y-m-d', $invoicePeriod->start)
        // );
        // $this->assertEquals(
        //     (new DateTime('first day of next month'))->format('Y-m-d'),
        //     date('Y-m-d', $invoicePeriod->end)
        // );
    }

    public function test_creating_subscription_with_trial()
    {
        $subscriber = $this->createCustomer();

        // Create Subscription
        $subscriber
            ->newSubscription(self::$basicPlan)
            ->monthly()
            ->quantity(1)
            ->trialDays(7)
            ->anchorBillingCycleOn(now()->addDays(7))
            ->add();

        $subscription = $subscriber->subscription();

        $this->assertTrue($subscription->active());
        $this->assertTrue($subscription->onTrial());
        $this->assertFalse($subscription->ended());
        $this->assertEquals(Carbon::today()->addDays(7)->day, $subscriber->trialEndsAt()->day);

        // Cancel Subscription
        $subscription->cancel();

        $this->assertTrue($subscription->active());
        $this->assertTrue($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // Resume Subscription
        $subscription->resume();

        $this->assertTrue($subscription->active());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertTrue($subscription->onTrial());
        $this->assertFalse($subscription->ended());
        $this->assertEquals(Carbon::today()->addDays(7)->day, $subscription->trial_ends_at->day);
    }

    public function test_user_without_subscriptions_can_return_its_generic_trial_end_date()
    {
        $user = new User;
        $user->trial_ends_at = $tomorrow = Carbon::tomorrow();

        $this->assertTrue($user->onGenericTrial());
        $this->assertSame($tomorrow, $user->trialEndsAt());
    }

    public function test_creating_subscription_with_explicit_trial()
    {
        $subscriber = $this->createCustomer();

        // Create Subscription
        $subscriber->newSubscription(self::$basicPlan)
            ->anchorBillingCycleOn(now()->tomorrow())
            ->trialUntil(Carbon::tomorrow()->hour(3)->minute(15))
            ->add();

        $subscription = $subscriber->subscription();

        $this->assertTrue($subscription->active());
        $this->assertTrue($subscription->onTrial());
        $this->assertFalse($subscription->ended());
        $this->assertEquals(Carbon::tomorrow()->hour(3)->minute(15), $subscription->trial_ends_at);

        // Cancel Subscription
        $subscription->cancel();

        $this->assertTrue($subscription->active());
        $this->assertTrue($subscription->onGracePeriod());
        $this->assertFalse($subscription->ended());

        // Resume Subscription
        $subscription->resume();

        $this->assertTrue($subscription->active());
        $this->assertFalse($subscription->onGracePeriod());
        $this->assertTrue($subscription->onTrial());
        $this->assertFalse($subscription->ended());
        $this->assertEquals(Carbon::tomorrow()->hour(3)->minute(15), $subscription->trial_ends_at);
    }

    /** @group FOO */
    public function test_trial_on_swap_is_skipped_when_explicitly_asked_to()
    {
        $subscriber = $this->createCustomer();

        $subscription = $subscriber->newSubscription(self::$basicPlan)
            ->trialDays(5)
            ->anchorBillingCycleOn(now()->addDays(5))
            ->add();

        $this->assertTrue($subscription->onTrial());

        $subscription = $subscription->skipTrial()->swap(self::$standardPlan);

        $this->assertFalse($subscription->onTrial());
    }

    public function test_trials_can_be_extended()
    {
        $subscriber = $this->createCustomer();

        $subscription = $subscriber
            ->newSubscription(self::$basicPlan)
            ->anchorBillingCycleOn(now()->addDays(5))
            ->add();

        $this->assertNull($subscription->trial_ends_at);

        $subscription->extendTrial($trialEndsAt = now()->addDays()->floor());

        $this->assertTrue($trialEndsAt->equalTo($subscription->trial_ends_at));
    }

    public function test_trials_extension_date_is_not_in_the_past()
    {
        $subscriber = $this->createCustomer();

        $subscription = $subscriber
            ->newSubscription(self::$basicPlan)
            ->anchorBillingCycleOn(now()->addDays(5))
            ->add();

        $this->assertNull($subscription->trial_ends_at);

        $this->expectException(InvalidArgumentException::class);

        $subscription->extendTrial($trialEndsAt = now()->subDays()->floor());
    }

    public function test_trials_can_be_ended()
    {
        $subscriber = $this->createCustomer();

        $subscription = $subscriber->newSubscription(self::$basicPlan)
            ->trialDays(10)
            ->anchorBillingCycleOn(now()->addDays(10))
            ->add();

        $subscription->endTrial();

        $this->assertNull($subscription->trial_ends_at);
    }

    public function test_subscription_state_scopes()
    {
        $subscriber = $this->createCustomer();

        // Start with an incomplete subscription.
        $subscription = $subscriber->newSubscription(self::$basicPlan)
            ->anchorBillingCycleOn(now()->addDays(10))
            ->add();
        $subscription->status = Subscription::STATUS_INACTIVE;
        $subscription->save();

        // Subscription is inactive
        $this->assertFalse($subscriber->subscriptions()->active()->exists());
        $this->assertFalse($subscriber->subscriptions()->onTrial()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->cancelled()->exists());
        $this->assertTrue($subscriber->subscriptions()->notCancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->onGracePeriod()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnGracePeriod()->exists());
        $this->assertFalse($subscriber->subscriptions()->ended()->exists());

        // Activate.
        $subscription->update(['status' => 'active']);

        $this->assertTrue($subscriber->subscriptions()->active()->exists());
        $this->assertFalse($subscriber->subscriptions()->onTrial()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->cancelled()->exists());
        $this->assertTrue($subscriber->subscriptions()->notCancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->onGracePeriod()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnGracePeriod()->exists());
        $this->assertFalse($subscriber->subscriptions()->ended()->exists());

        // Put on trial.
        $subscription->update(['trial_ends_at' => Carbon::now()->addDay()]);

        $this->assertTrue($subscriber->subscriptions()->active()->exists());
        $this->assertTrue($subscriber->subscriptions()->onTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->notOnTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->cancelled()->exists());
        $this->assertTrue($subscriber->subscriptions()->notCancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->onGracePeriod()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnGracePeriod()->exists());
        $this->assertFalse($subscriber->subscriptions()->ended()->exists());

        // Put on grace period.
        $subscription->update(['ends_at' => Carbon::now()->addDay()]);

        $this->assertTrue($subscriber->subscriptions()->active()->exists());
        $this->assertTrue($subscriber->subscriptions()->onTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->notOnTrial()->exists());
        $this->assertTrue($subscriber->subscriptions()->cancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->notCancelled()->exists());
        $this->assertTrue($subscriber->subscriptions()->onGracePeriod()->exists());
        $this->assertFalse($subscriber->subscriptions()->notOnGracePeriod()->exists());
        $this->assertFalse($subscriber->subscriptions()->ended()->exists());

        // End subscription.
        $subscription->update(['ends_at' => Carbon::now()->subDay()]);

        $this->assertFalse($subscriber->subscriptions()->active()->exists());
        $this->assertTrue($subscriber->subscriptions()->onTrial()->exists());
        $this->assertFalse($subscriber->subscriptions()->notOnTrial()->exists());
        $this->assertTrue($subscriber->subscriptions()->cancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->notCancelled()->exists());
        $this->assertFalse($subscriber->subscriptions()->onGracePeriod()->exists());
        $this->assertTrue($subscriber->subscriptions()->notOnGracePeriod()->exists());
        $this->assertTrue($subscriber->subscriptions()->ended()->exists());

        // Enable past_due as active state.
        $this->assertFalse($subscription->active());
        $this->assertFalse($subscriber->subscriptions()->active()->exists());

        Paysub::keepPastDueSubscriptionsActive();

        $subscription->update(['ends_at' => null, 'status' => Subscription::STATUS_PAST_DUE]);

        $this->assertTrue($subscription->pastDue());
        $this->assertTrue($subscriber->subscriptions()->active()->exists());

        // Reset deactivate past due state to default to not conflict with other tests.
        Paysub::$deactivatePastDue = true;
    }
}
