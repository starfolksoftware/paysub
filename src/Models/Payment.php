<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;

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
    protected $casts = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config('paysub.payment_table_name', parent::getTable());
    }

    /**
     * Get the invoice of the payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Get the invoice of the payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function authorization()
    {
        return $this->belongsTo(Authorization::class, 'authorization_id');
    }

    /**
     * Get the subscription of the payment
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function subscription()
    {
        return $this->belongsToThrough(
            'StarfolkSoftware\Paysub\Models\Subscription',
            'StarfolkSoftware\Paysub\Models\Invoice',
            null,
            '',
            [
                'StarfolkSoftware\Paysub\Models\Subscription' => 'subscription_id',
                'StarfolkSoftware\Paysub\Models\Subscription' => 'invoice_id',
            ]
        );
    }
}
