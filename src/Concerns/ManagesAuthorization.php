<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Authorization;

trait ManagesAuthorization {
    /**
     * Get all of the authorizations for the subscriber model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authorizations()
    {
        return $this->hasMany(Authorization::class, 'subscriber_id')->orderBy('created_at', 'desc');
    }
}
