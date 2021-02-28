<?php

namespace StarfolkSoftware\Paysub\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait ManagesCard
{
    /**
     * Get the cards
     *
     * @return array
     */
    public function cards()
    {
        $authorizations = $this->authorizations;

        $cards = $authorizations->map(function ($authorization) {
            return $authorization->card;
        });

        return Collection::unwrap($cards);
    }
}
