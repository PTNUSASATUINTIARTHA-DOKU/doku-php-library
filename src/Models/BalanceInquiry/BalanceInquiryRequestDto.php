<?php

namespace Doku\Snap\Models\BalanceInquiry;

class BalanceInquiryRequestDto
{
    public BalanceInquiryAdditionalInfoRequestDto $additionalInfo;

    public function __construct(BalanceInquiryAdditionalInfoRequestDto $additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    public function validateBalanceInquiryRequestDto(): void
    {
        $this->additionalInfo->validate();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'additionalInfo' => array(
                'channel' => $this->additionalInfo->channel,
                'origin' => $this->additionalInfo->origin->toArray()
            )
        ]);
    }
}