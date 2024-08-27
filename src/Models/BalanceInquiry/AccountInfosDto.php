<?php

namespace Doku\Snap\Models\BalanceInquiry;
use Doku\Snap\Models\TotalAmount\TotalAmount;

class AccountInfosDto
{
    public string $balanceType;
    public TotalAmount $amount;
    public TotalAmount $flatAmount;
    public TotalAmount $holdAmount;

    public function __construct(string $balanceType, TotalAmount $amount, TotalAmount $flatAmount, TotalAmount $holdAmount)
    {
        $this->balanceType = $balanceType;
        $this->amount = $amount;
        $this->flatAmount = $flatAmount;
        $this->holdAmount = $holdAmount;
    }
}