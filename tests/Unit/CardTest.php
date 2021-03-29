<?php

namespace StarfolkSoftware\Paysub\Tests\Unit;

use StarfolkSoftware\Paysub\Models\Card;
use StarfolkSoftware\Paysub\Tests\TestCase;

class CardTest extends TestCase
{
    public function test_we_can_get_the_table_name()
    {
        $card = new Card;

        $this->assertEquals($card->getTable(), config(
            'paysub.card_table_name'
        ));
    }

    public function test_if_card_has_expired()
    {
        $card = new Card([
            'exp_year' => '2019',
            'exp_month' => '7',
        ]);

        $this->assertTrue($card->expired());
    }
}
