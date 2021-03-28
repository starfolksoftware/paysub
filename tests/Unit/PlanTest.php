<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Plan;
use StarfolkSoftware\Paysub\Tests\TestCase;

class PlanTest extends TestCase {
    public function test_we_can_get_the_table_name() {
        $plan = new Plan;

        $this->assertEquals($plan->getTable(), config(
            'paysub.plan_table_name'
        ));
    }
}
