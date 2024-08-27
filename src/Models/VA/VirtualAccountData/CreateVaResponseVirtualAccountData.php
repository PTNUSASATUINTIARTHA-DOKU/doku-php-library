<?php
namespace Doku\Snap\Models\VA\VirtualAccountData;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\AdditionalInfo\CreateVaResponseAdditionalInfo;
class CreateVaResponseVirtualAccountData
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $virtualAccountName;
    public ?string $virtualAccountEmail;
    public ?string $trxId;
    public TotalAmount $totalAmount;
    public ?string $virtualAccountTrxType;
    public ?string $expiredDate;
    public CreateVaResponseAdditionalInfo $additionalInfo;

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $virtualAccountName,
        ?string $virtualAccountEmail,
        ?string $trxId,
        ?TotalAmount $totalAmount,
        ?string $virtualAccountTrxType,
        ?string $expiredDate,
        ?CreateVaResponseAdditionalInfo $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->trxId = $trxId;
        $this->totalAmount = $totalAmount;
        $this->virtualAccountTrxType = $virtualAccountTrxType;
        $this->expiredDate = $expiredDate;
        $this->additionalInfo = $additionalInfo;
    }
}