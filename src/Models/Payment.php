<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
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

    public function getTable() {
        return config('paysub.payment_table_name', parent::getTable());
    }

    /**
     * Get the subscription of the payment
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription() {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get the authorization of the payment
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function authorization() {
        return $this->belongsTo(Authorization::class, 'authorization_id');
    }
}
