<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Concerns\Prorates;

class SubscriptionItem extends Model
{
    use Prorates;
    
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
        'quantity' => 'integer',
    ];

    public function getTable()
    {
        return config('paysub.subscription_items_table_name', parent::getTable());
    }

    /**
     * Get the subscription that the item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get the plan of the subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get usages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usages()
    {
        return $this->hasMany(
            Usage::class,
            'item_id'
        );
    }

    /**
     * Increment the quantity of the subscription item.
     *
     * @param  int  $count
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function incrementQuantity($count = 1)
    {
        $this->updateQuantity($this->quantity + $count);

        return $this;
    }

    /**
     *  Increment the quantity of the subscription item, and invoice immediately.
     *
     * @param  int  $count
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\IncompletePayment
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function incrementAndInvoice($count = 1)
    {
        $this->alwaysInvoice();

        $this->incrementQuantity($count);

        return $this;
    }

    /**
     * Decrement the quantity of the subscription item.
     *
     * @param  int  $count
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function decrementQuantity($count = 1)
    {
        $this->updateQuantity(max(1, $this->quantity - $count));

        return $this;
    }

    /**
     * Update the quantity of the subscription item.
     *
     * @param  int  $quantity
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;

        $this->save();

        if ($this->subscription->hasSinglePlan()) {
            $this->subscription->quantity = $quantity;

            $this->subscription->save();
        }

        return $this;
    }

    /**
     * Swap the subscription item to a new plan.
     *
     * @param  Plan  $plan
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function swap($plan)
    {
        $this->fill([
            'plan_id' => $plan->id,
            'quantity' => $this->quantity,
        ])->save();

        if ($this->subscription->hasSinglePlan()) {
            $this->subscription->fill([
                'plan_id' => $plan->id,
                'quantity' => $this->quantity,
            ])->save();
        }

        return $this;
    }

    /**
     * Swap the subscription item to a new plan, and invoice immediately.
     *
     * @param  Plan  $plan
     * @return $this
     *
     * @throws \StarfolkSoftware\Paysub\Exceptions\SubscriptionUpdateFailure
     */
    public function swapAndInvoice($plan)
    {
        $this->alwaysInvoice();

        return $this->swap($plan);
    }
}
