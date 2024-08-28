<?php

namespace Doku\Snap\Models\BalanceInquiry;

class BalanceInquiryAdditionalInfoRequestDto
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }
}