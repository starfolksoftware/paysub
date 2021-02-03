<?php

namespace StarfolkSoftware\Paysub\Concerns;

use Illuminate\Database\Eloquent\Collection;
use StarfolkSoftware\Paysub\Models\Authorization;
use StarfolkSoftware\Paysub\Models\Card;

trait ManagesCard
{
    /**
     * Get the cards
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function cards()
    {
        $authorizations = $this->authorizations;

        $cards = $authorizations->map(function($authorization) {
            return $authorization->card;
        });

        // return Collection::unwrap($cards);
        return $cards;
    }
}
