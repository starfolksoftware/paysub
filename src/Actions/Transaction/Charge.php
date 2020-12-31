<?php

namespace StarfolkSoftware\Paysub\Actions\Transaction;

use StarfolkSoftware\Paysub\Api\Transaction;

class Charge
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->charge($fields);
    }
}
