<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Feature;
use StarfolkSoftware\Paysub\Tests\TestCase;

class FeatureTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $feature = new Feature;

        $this->assertEquals($feature->getTable(), config(
            'paysub.feature_table_name'
        ));
    }
}
