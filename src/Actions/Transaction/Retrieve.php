<?php

namespace StarfolkSoftware\Paysub\Actions\Transaction;

use StarfolkSoftware\Paysub\Api\Transaction;

class Retrieve
{
    public function execute(string $id)
    {
        $transaction = new Transaction();

        return $transaction->retrieve($id);
    }
}
