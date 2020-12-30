<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Transaction;

use StarfolkSoftware\PaystackSubscription\Api\Transaction;

class Charge
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->charge($fields);
    }
}
