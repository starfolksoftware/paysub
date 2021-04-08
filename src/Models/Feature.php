<?php

namespace StarfolkSoftware\Paysub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    
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
            'paysub.feature_table_name',
            parent::getTable()
        );
    }

    /**
     * Get plans
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            config('paysub.feature_plan_table_name'),
            'feature_id',
            'plan_id'
        )->withPivot('value');
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
            'feature_id'
        );
    }
}
