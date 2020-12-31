<?php

namespace StarfolkSoftware\Paysub\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Models\Subscription;

class Invoice extends Model {
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
        'tax' => Json::class,
    ];

    /**
     * The attributes that should be appended
     */
    protected $appends = [
        'amount'
    ];

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

    public function getTable() {
        return config('paysub.invoice_table_name', parent::getTable());
    }

    /**
     * Get the subscription of the invoice
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription() {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Calculate the invoice amount
     */
    public function getAmountAttribute() {
        $totalTax = collect($this->tax)->reduce(function($carry, $tax) {
            return $carry + $tax['amount'];
        }, 0);
        
        $planAmount = $this->subscription->plan->amount;
        $quantity = $this->subscription->quantity;

        return ($planAmount * $quantity) - $totalTax;
    }
}
