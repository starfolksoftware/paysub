<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Models\Subscription;

class Plan extends Model {
    const INTERVAL_MONTHLY = 'monthly';
    const INTERVAL_YEARLY = 'yearly';

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
        'created_at',
        'updated_at',
    ];

    public function getTable() {
        return config('paysub.plan_table_name', parent::getTable());
    }

    /**
     * Get the subscriptions of the plan
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions() {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
