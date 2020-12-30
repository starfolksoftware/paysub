<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Transaction;

use StarfolkSoftware\PaystackSubscription\Api\Transaction;

class RetrieveAll
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->all($fields);
    }
}
