<?php
namespace Doku\Snap\Models\Payment;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
class PayOptionDetailsDto
{
    public ?string $payMethod;
    public ?TotalAmount $transAmount;
    public ?TotalAmount $feeAmount;

    public function __construct(?string $payMethod, ?TotalAmount $transAmount, ?TotalAmount $feeAmount)
    {
        $this->payMethod = $payMethod;
        $this->transAmount = $transAmount;
        $this->feeAmount = $feeAmount;
    }
}