<?php

namespace StarfolkSoftware\Paysub\Actions\Transaction;

use StarfolkSoftware\Paysub\Api\Transaction;

class Initialize
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->initialize($fields);
    }
}
