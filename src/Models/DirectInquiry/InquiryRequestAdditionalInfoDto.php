<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryRequestAdditionalInfoDto
{
    public ?string $channel;
    public ?string $trxId;

    public function __construct(?string $channel, ?string $trxId)
    {
        $this->channel = $channel;
        $this->trxId = $trxId;
    }
}