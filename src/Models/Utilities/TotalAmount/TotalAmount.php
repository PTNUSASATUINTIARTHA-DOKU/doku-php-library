<?php
namespace Doku\Snap\Models\Utilities\TotalAmount;
class TotalAmount
{
    public ?string $value;
    public ?string $currency;

    /**
     * TotalAmount constructor
     * @param string $value The total amount value
     * @param string $currency The currency of the total amount
     */
    public function __construct(?string $value, ?string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }
}