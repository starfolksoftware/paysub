<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;
use StarfolkSoftware\Paysub\Casts\Json;

class Authorization extends Model
{
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
        'auth' => Json::class,
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
        'created_at',
        'updated_at',
    ];

    public function getTable()
    {
        return config(
            'paysub.auth_table_name',
            parent::getTable()
        );
    }

    /**
     * Get the subscribers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(
            config('paysub.subscriber_model'),
            config('paysub.auth_table_name').'_'.config('paysub.subscriber_table_name'),
            'subscriber_id',
            'authorization_id'
        )->withPivot('default');
    }

    /**
     * Get payments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(
            Payment::class,
            'authorization_id'
        );
    }
}
