<?php

namespace StarfolkSoftware\Paysub;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use StarfolkSoftware\Paysub\Events\InvoicePaid;
use StarfolkSoftware\Paysub\Events\SubscriptionCancelled;
use StarfolkSoftware\Paysub\Events\SubscriptionCreated;
use StarfolkSoftware\Paysub\Listeners\GenerateFirstInvoice;
use StarfolkSoftware\Paysub\Listeners\GenerateUpcomingInvoice;
use StarfolkSoftware\Paysub\Listeners\MarkInvoiceAsPaid;
use StarfolkSoftware\Paysub\Listeners\MarkInvoiceAsVoid;
use StarfolkSoftware\Paysub\Listeners\ResumeSubscription;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionCreated::class => [
            GenerateFirstInvoice::class,
        ],
        InvoicePaid::class => [
            MarkInvoiceAsPaid::class,
            ResumeSubscription::class,
            GenerateUpcomingInvoice::class,
        ],
        SubscriptionCancelled::class => [
            MarkInvoiceAsVoid::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
