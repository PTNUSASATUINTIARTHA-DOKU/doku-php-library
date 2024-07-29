<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryRequestAdditionalInfoDto
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }
}