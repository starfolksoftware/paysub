<?php

namespace StarfolkSoftware\PaystackSubscription\Concerns;

use \InvalidArgumentException;
use StarfolkSoftware\PaystackSubscription\Actions\Transaction\Initialize as PaystackTransactionInit;

trait ManagesTransaction
{
    /**
     * @return \StarfolkSoftware\PaystackSubscription\Api\Transaction;
     *
     * @param string $plan
     * @param array $channels
     */
    public function initTransWithPlanSub(string $plan, array $channels = ['card'])
    {
        if (! $plan) {
            throw new InvalidArgumentException("plan code is not provided");
        }

        return (new PaystackTransactionInit())->execute([
            'email' => $this->paystackEmail(),
            'plan' => $plan,
            'channels' => $channels,
        ]);
    }
}
