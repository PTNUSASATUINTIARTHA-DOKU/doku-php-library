<?php
namespace Doku\Snap\Models\DirectInquiry;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\CreateVaVirtualAccountConfig;
class InquiryResponseAdditionalInfoDto
{
    public string $channel;
    public string $trxId;
    public CreateVaVirtualAccountConfig $virtualAccountConfig;

    public function __construct(string $channel, string $trxId, CreateVaVirtualAccountConfig $virtualAccountConfig)
    {
        $this->channel = $channel;
        $this->trxId = $trxId;
        $this->virtualAccountConfig = $virtualAccountConfig;
    }
}