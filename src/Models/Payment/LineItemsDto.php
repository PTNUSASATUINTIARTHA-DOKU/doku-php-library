<?php
namespace Doku\Snap\Models\Payment;
class LineItemsDto
{
    public ?string $name;
    public ?string $price;
    public ?string $quantity;

    public function __construct(?string $name, ?string $price, ?string $quantity)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function generateJSONBody()
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity
        ];
    }
}