<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use StarfolkSoftware\Paysub\Models\Invoice;

class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;

    protected $billable;
    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @param mixed $billable
     * @param Invoice $invoice
     */
    public function __construct($billable, Invoice $invoice)
    {
        $this->billable = $billable;
        $this->invoice  = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = [
            'vendor' => config('paysub.contact_detail.vendor'),
            'street' => config('paysub.contact_detail.street'),
            'location' => config('paysub.contact_detail.location'),
            'phone' => config('paysub.contact_detail.phone'),
            'url' => config('paysub.contact_detail.url'),
            'vatInfo' => config('paysub.contact_detail.vatInfo'),
        ];

        $mailMessage = (new MailMessage)->subject('Ciniki Accounting' . ' Invoice[INV-'.$this->invoice->id.']')
                                        ->view('paysub::invoice', array_merge($data, [
                                            'invoice' => $this,
                                            'subscription' => $this->invoice->subscription
                                        ]))
                                        ->line('Thanks for your continued support. We\'ve attached a copy of your invoice for your records. Please let us know if you have any questions or concerns!')
                                        ->attachData($this->invoice->pdf($data), 'invoice.pdf');

        return $mailMessage;
    }
}
