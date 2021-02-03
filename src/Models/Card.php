<?php

namespace StarfolkSoftware\Paysub\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Card extends Model {
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
        'exp_year' => 'integer'
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
            'paysub.card_table_name', 
            parent::getTable()
        );
    }

    /**
     * Get authorizations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authorizations() {
        return $this->hasMany(
            Authorization::class,
            'card_id'
        );
    }

    /**
     * Check if card has expired
     * 
     * @return bool
     */
    public function expired() {
        $date = Carbon::create($this->exp_year, $this->exp_month);

        return now()->isAfter($date);
    }
}
