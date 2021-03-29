<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Authorization;
use StarfolkSoftware\Paysub\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $authorization = new Authorization;

        $this->assertEquals($authorization->getTable(), config(
            'paysub.auth_table_name'
        ));
    }
}
