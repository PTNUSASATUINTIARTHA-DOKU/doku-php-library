<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryRequestAdditionalInfo
{
    public ?string $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}