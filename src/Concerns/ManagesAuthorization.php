<?php

namespace StarfolkSoftware\Paysub\Concerns;

use StarfolkSoftware\Paysub\Models\Authorization;

trait ManagesAuthorization
{
    /**
     * Get the subscribers
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function authorizations()
    {
        return $this->hasMany(
            Authorization::class,
            'subscriber_id'
        );
    }

    /**
     * Set authorization as default
     *
     * @param string $signature
     * @return int
     */
    public function setDefaultAuth(Authorization $auth)
    {
        $oldDefault = $this->authorizations()->where('default', true)->first();

        $result = $auth->update([
            'default' => true,
        ]);

        if ($result && $oldDefault) {
            $oldDefault->update([
                'default' => false,
            ]);
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
        $default = $this->authorizations()->where('default', true)->first();

        if (! $default && ($this->authorizations()->count() > 0)) {
            $this->setDefaultAuth($this->authorizations()->first());

            return $this->authorizations()->first();
        }

        return $default;
    }
}
