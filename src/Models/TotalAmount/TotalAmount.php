<?php
namespace Doku\Snap\Models\TotalAmount;
class TotalAmount
{
    public ?string $value;
    public ?string $currency;

    public function __construct(?string $value, ?string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    public function generateJSONBody(): array
    {
        return [
            'value' => $this->value,
            'currency' => $this->currency
        ];
    }
}