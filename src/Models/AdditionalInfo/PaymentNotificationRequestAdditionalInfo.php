<?php
namespace Doku\Snap\Models\AdditionalInfo;
class PaymentNotificationRequestAdditionalInfo {
    public ?string $channel;
    public ?string $senderName;
    public ?string $sourceAccountNo;
    public ?string $sourceBankCode;
    public ?string $souceBankName;

    public function __construct(?string $channel, ?string $senderName, ?string $sourceAccountNo, ?string $sourceBankCode, ?string $souceBankName)
    {
        $this->channel = $channel;
        $this->senderName = $senderName;
        $this->sourceAccountNo = $sourceAccountNo;
        $this->sourceBankCode = $sourceBankCode;
        $this->souceBankName = $souceBankName;
    }
}