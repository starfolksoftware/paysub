<?php

namespace StarfolkSoftware\Paysub\Tests\Feature;

use StarfolkSoftware\Paysub\Models\Plan;

class PlanTest extends FeatureTestCase
{
    /**
     * @var Plan
     */
    protected static $basicPlan;

    public function setUp(): void
    {
        parent::setUp();

        self::$basicPlan = Plan::create([
            'name' => 'basic',
            'display_name' => 'Basic',
            'description' => '',
            'interval_type' => 'monthly',
            'interval_count' => 1,
            'amount' => 250000,
        ]);
    }

    public function test_plan_has_feature()
    {
        self::$basicPlan->features()->create([
            'name' => 'sample_feature',
            'sort_order' => 1,
        ]);

        $this->assertTrue(self::$basicPlan->hasFeature('sample_feature'));
    }
}
