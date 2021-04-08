<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Casts\Json;

class Plan extends Model
{
    use HasFactory;

    const INTERVAL_DAILY = 'daily';
    const INTERVAL_WEEKLY = 'weekly';
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
    protected $casts = [
        'tax_rates' => Json::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config('paysub.plan_table_name', parent::getTable());
    }

    /**
     * Get the subscriptions of the plan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Get the items of the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(SubscriptionItem::class, 'plan_id');
    }

    /**
     * Get features
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function features()
    {
        return $this->belongsToMany(
            Feature::class,
            config('paysub.feature_plan_table_name'),
            'plan_id',
            'feature_id'
        )->withPivot('value');
    }

    /**
     * Check if plan has feature
     * 
     * @param string $name
     * @return bool
     */
    public function hasFeature($name) {
        return !! $this->features()->where('name', $name)->first();
    }
}
