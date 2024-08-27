<?php

namespace Doku\Snap\Models\BalanceInquiry;
use Doku\Snap\Models\AdditionalInfo\BalanceInquiryAdditionalInfoRequestDto;

class BalanceInquiryRequestDto
{
    public BalanceInquiryAdditionalInfoRequestDto $additionalInfo;

    public function __construct(BalanceInquiryAdditionalInfoRequestDto $additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    public function validateBalanceInquiryRequestDto(): void
    {
        // logic
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'additionalInfo' => $this->additionalInfo
        ]);
    }
}