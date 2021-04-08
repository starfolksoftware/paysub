<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Usage;
use StarfolkSoftware\Paysub\Tests\TestCase;

class UsageTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $usage = new Usage;

        $this->assertEquals($usage->getTable(), config(
            'paysub.usage_table_name'
        ));
    }
}
