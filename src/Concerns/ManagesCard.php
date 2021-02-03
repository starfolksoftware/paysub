<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Authorization;
use StarfolkSoftware\Paysub\Models\Card;

trait ManagesCard
{
    /**
     * Get the cards
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function cards()
    {
        return $this->hasManyThrough(
            Card::class,
            Authorization::class,
            'subscriber_id',
            'authorization_id',
            'id',
            'id'
        );
    }
}
