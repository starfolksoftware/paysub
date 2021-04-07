<?php

namespace StarfolkSoftware\Paysub\Models;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use StarfolkSoftware\Paysub\Casts\Json;
use StarfolkSoftware\Paysub\Paysub;
use Symfony\Component\HttpFoundation\Response;

class Invoice extends Model
{
    use HasFactory;
    
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_VOID = 'void';
    
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'line_items' => Json::class,
        'tax' => Json::class,
    ];

    /**
     * The attributes that should be appended
     */
    protected $appends = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'due_date',
        'paid_at',
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config('paysub.invoice_table_name', parent::getTable());
    }

    /**
     * Get the subscription of the invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get the payments of the authorization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    /**
     * Filter query by paid.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePaid($query)
    {
        $query->where('status', self::STATUS_PAID);
    }

    /**
     * Filter query by unpaid.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeUnpaid($query)
    {
        $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Filter query by void.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeVoid($query)
    {
        $query->where('status', self::STATUS_VOID);
    }

    /**
     * Filter query by void or not paid.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeVoidOrUnpaid($query)
    {
        $query->where('status', self::STATUS_VOID)->orWhere('status', self::STATUS_UNPAID);
    }

    /**
     * Filter query by past due.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePastDue($query)
    {
        $query->whereNotNull('due_date')->where('due_date', '>', Carbon::now());
    }

    /**
     * Get a Carbon date for the invoice.
     *
     * @param  \DateTimeZone|string  $timezone
     * @return \Carbon\Carbon
     */
    public function date($timezone = null)
    {
        $carbon = Carbon::createFromTimestampUTC($this->created_at);

        return $timezone ? $carbon->setTimezone($timezone) : $carbon;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @param string $currency
     * @return string
     */
    protected function formatAmount($amount, $currency = 'NGN')
    {
        return Paysub::formatAmount(
            $amount,
            $currency ?? $this->subscription->plan->currency
        );
    }

    /**
     * Get the View instance for the invoice.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\View\View
     */
    public function view(array $data)
    {
        return View::make('paysub::invoice', array_merge($data, [
            'invoice' => $this,
            'subscription' => $this->subscription,
        ]));
    }

    /**
     * Capture the invoice as a PDF and return the raw bytes.
     *
     * @param  array  $data
     * @return string
     */
    public function pdf(array $data)
    {
        if (! defined('DOMPDF_ENABLE_AUTOLOAD')) {
            define('DOMPDF_ENABLE_AUTOLOAD', false);
        }

        $dompdf = new Dompdf;
        $dompdf->setPaper(config('paysub.paper', 'letter'));
        $dompdf->loadHtml($this->view($data)->render());
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Create an invoice download response.
     *
     * @param  array  $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download(array $data)
    {
        $filename = $data['product'].'_'.$this->date()->month.'_'.$this->date()->year;

        return $this->downloadAs($filename, $data);
    }

    /**
     * Create an invoice download response with a specific filename.
     *
     * @param  string  $filename
     * @param  array  $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadAs($filename, array $data)
    {
        return new Response($this->pdf($data), 200, [
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.pdf"',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => 'application/pdf',
            'X-Vapor-Base64-Encode' => 'True',
        ]);
    }

    /**
     * mark invoice as void.
     *
     * @return $this
     */
    public function void()
    {
        $this->status = self::STATUS_VOID;
        $this->save();

        return $this;
    }

    /**
     * mark invoice as paid.
     *
     * @return $this
     */
    public function markAsPaid()
    {
        $this->status = self::STATUS_PAID;
        $this->save();

        return $this;
    }

    /**
     * mark invoice as paid.
     *
     * @return $this
     */
    public function markAsUnpaid()
    {
        $this->status = self::STATUS_UNPAID;
        $this->save();

        return $this;
    }

    /**
     * Get the Subscriber model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function owner()
    {
        return $this->subscription->subscriber;
    }
}
