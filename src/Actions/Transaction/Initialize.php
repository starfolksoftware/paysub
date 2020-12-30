<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Transaction;

use StarfolkSoftware\PaystackSubscription\Api\Transaction;

class Initialize
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->initialize($fields);
    }
}
