<?php

namespace Doku\Snap\Models\BalanceInquiry;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;

class BalanceInquiryAdditionalInfoRequestDto
{
    public string $channel;
    public Origin $origin;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
        $this->origin = new Origin();
    }
    public function validate()
    {
        if (empty($this->channel)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.channel is required'
            ];
        }
    }
}