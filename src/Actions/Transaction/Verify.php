<?php

namespace StarfolkSoftware\PaystackSubscription\Actions\Transaction;

use StarfolkSoftware\PaystackSubscription\Api\Transaction;

class Verify
{
    public function execute(array $reference)
    {
        $transaction = new Transaction();

        return $transaction->verify($reference);
    }
}
