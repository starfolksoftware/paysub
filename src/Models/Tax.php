<?php

namespace StarfolkSoftware\Paysub\Models;

use StarfolkSoftware\Paysub\Paysub;

class Tax
{
    /**
     * The total tax amount.
     *
     * @var int
     */
    protected $amount;

    /**
     * The applied currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * The TaxRate object.
     *
     * @var Object
     */
    protected $taxRate;

    /**
     * Create a new Tax instance.
     *
     * @param  int  $amount
     * @param  string  $currency
     * @param  object  $taxRate
     * @return void
     */
    public function __construct($amount, $currency, $taxRate)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->taxRate = (object) $taxRate;
    }

    /**
     * Get the applied currency.
     *
     * @return string
     */
    public function currency()
    {
        return $this->currency;
    }

    /**
     * Get the total tax that was paid (or will be paid).
     *
     * @return string
     */
    public function amount()
    {
        return $this->formatAmount($this->amount);
    }

    /**
     * Get the raw total tax that was paid (or will be paid).
     *
     * @return int
     */
    public function rawAmount()
    {
        return $this->amount;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @return string
     */
    protected function formatAmount($amount)
    {
        return Paysub::formatAmount($amount, $this->currency);
    }

    /**
     * Determine if the tax is inclusive or not.
     *
     * @return bool
     */
    public function isInclusive()
    {
        return $this->taxRate->inclusive;
    }

    /**
     * @return object
     */
    public function taxRate()
    {
        return $this->taxRate;
    }

    /**
     * Dynamically get values from the Stripe TaxRate.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->taxRate->{$key};
    }
}
