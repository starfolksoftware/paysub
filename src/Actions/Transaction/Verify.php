<?php

namespace StarfolkSoftware\Paysub\Actions\Transaction;

use StarfolkSoftware\Paysub\Api\Transaction;

class Verify
{
    public function execute(array $reference)
    {
        $transaction = new Transaction();

        return $transaction->verify($reference);
    }
}
