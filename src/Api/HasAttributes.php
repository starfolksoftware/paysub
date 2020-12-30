<?php

namespace StarfolkSoftware\PaystackSubscription\Api;

trait HasAttributes
{
    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    private function setAttribute($key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }

    /**
     * Set attributes on the model.
     *
     * @param  array  $attributes
     * @return mixed
     */
    private function setAttributes(array $attributes)
    {
        collect($attributes)->each(function ($value, $key) {
            $this->setAttribute($key, $value);
        });

        return $this;
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->{$key};
    }
}
