<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Transaction;

use StarfolkSoftware\PaystackSubscription\Api\Transaction;

class Retrieve
{
    public function execute(string $id)
    {
        $transaction = new Transaction();

        return $transaction->retrieve($id);
    }
}
