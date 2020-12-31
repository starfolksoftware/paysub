<?php

namespace StarfolkSoftware\Paysub\Actions\Transaction;

use StarfolkSoftware\Paysub\Api\Transaction;

class RetrieveAll
{
    public function execute(array $fields)
    {
        $transaction = new Transaction();

        return $transaction->all($fields);
    }
}
