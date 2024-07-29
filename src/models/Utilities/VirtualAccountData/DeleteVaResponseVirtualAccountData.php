<?php
namespace Doku\Snap\Models\Utilities\VirtualAccountData;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaResponseAdditionalInfo;
class DeleteVaResponseVirtualAccountData
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $trxId;
    public ?DeleteVaResponseAdditionalInfo $additionalInfo;

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $trxId,
        ?DeleteVaResponseAdditionalInfo $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->trxId = $trxId;
        $this->additionalInfo = $additionalInfo;
    }
}