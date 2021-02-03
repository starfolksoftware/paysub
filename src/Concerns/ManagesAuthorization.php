<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Authorization;

trait ManagesAuthorization
{
    /**
     * Get the subscribers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authorizations()
    {
        return $this->belongsToMany(
            config('paysub.auth_table_name'),
            config('paysub.auth_table_name').'_'.config('paysub.subscriber_table_name'),
            'authorization_id',
            'subscriber_id'
        )->withPivot('default');
    }

    /**
     * Set authorization as default
     *
     * @param string $signature
     * @return int
     */
    public function setDefaultAuth(Authorization $auth)
    {
        $oldDefault = $this->authorizations()->wherePivot('default', true)->first();

        $result = $this->authorizations()->updateExistingPivot($auth->id, [
            'default' => 1,
        ], false);

        if ($result && $oldDefault) {
            $this->authorizations()->updateExistingPivot($oldDefault->id, [
                'default' => 1,
            ], false);
        }

        return $result;
    }

    /**
     * Get default Authorization
     *
     * @return Authorization|null
     */
    public function defaultAuth()
    {
        $default = $this->authorizations()->wherePivot('default', true)->first();

        if (! $default && ($this->authorizations()->count() > 0)) {
            $this->setDefaultAuth($this->authorizations()->first());

            return $this->authorizations()->first();
        }

        return $default;
    }
}
