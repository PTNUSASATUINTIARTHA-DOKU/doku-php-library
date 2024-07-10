<?php
/**
 * Class VirtualAccountData
 * Represents the virtual account data
 */
class CreateVaResponseVirtualAccountData
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $virtualAccountName;
    public ?string $virtualAccountEmail;
    public ?string $trxId;
    public ?TotalAmount $totalAmount;
    public ?CreateVaResponseAdditionalInfo $additionalInfo;

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $virtualAccountName,
        ?string $virtualAccountEmail,
        ?string $trxId,
        ?TotalAmount $totalAmount,
        ?CreateVaResponseAdditionalInfo $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->trxId = $trxId;
        $this->totalAmount = $totalAmount;
        $this->additionalInfo = $additionalInfo;
    }
}