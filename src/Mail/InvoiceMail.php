<?php
// 'src/Mail/WelcomeMail.php'

namespace StarfolkSoftware\Paysub\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use StarfolkSoftware\Paysub\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $billable;
    protected $invoice;

    /**
     * Create a new mail instance.
     *
     * @param mixed $billable
     * @param Invoice $invoice
     */
    public function __construct($billable, Invoice $invoice)
    {
        $this->billable = $billable;
        $this->invoice = $invoice;
    }

    /**
     * @return static
     */
    public function build(): self
    {
        $data = [
            'vendor' => '',
            'street' => '',
            'location' => '',
            'phone' => '',
            'url' => '',
            'vatInfo' => '',
        ];

        return $this->view('paysub::invoice')->with(array_merge($data, [
            'invoice' => $this,
            'subscription' => $this->invoice->subscription,
        ]))->attachData($this->invoice->pdf($data), 'invoice.pdf', [
            'mime' => 'application/pdf',
        ]);
    }
}
