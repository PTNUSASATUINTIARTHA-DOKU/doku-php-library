<?php

namespace Doku\Snap\Models\BalanceInquiry;

class BalanceInquiryAdditionalInfoRequestDto
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }
    public function validate(): void {
        if (is_null($this->channel) || trim($this->channel) === '') {
            throw new \InvalidArgumentException("additionalInfo.channel cannot be null. Ensure that additionalInfo.channel is one of the valid channels. Example: 'DIRECT_DEBIT_ALLO_SNAP'.");
        }
    }
}