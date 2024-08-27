<?php
namespace Doku\Snap\Models\AdditionalInfo;
class InquiryRequestAdditionalInfo
{
    public ?string $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}