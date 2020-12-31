<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model {
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
        'exp_month' => 'integer',
        'exp_year' => 'integer',
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

    public function getTable() {
        return config('paysub.authorization_table_name', parent::getTable());
    }

    /**
     * Get the owner of the authorization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        $model = config('paysub.subscriber_model');

        return $this->belongsTo($model, 'subscriber_id');
    }

    /**
     * Get the payments of the authorization
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments() {
        return $this->hasMany(Payment::class, 'authorization_id');
    }
}
