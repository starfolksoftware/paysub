<?php

namespace StarfolkSoftware\PaystackSubscription\Commands;

use Illuminate\Console\Command;

class SubscriptionCommand extends Command
{
    public $signature = 'paystack-subscription';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
