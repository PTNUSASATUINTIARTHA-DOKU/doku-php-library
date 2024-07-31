<?php
namespace Doku\Snap\Models\DirectInquiry;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
class InquiryRequestVirtualAccountDataDto
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $virtualAccountEmail;
    public string $virtualAccountPhone;
    public TotalAmount $totalAmount;
    public string $virtualAccountTrxType;
    public string $expiredDate;
    public InquiryRequestAdditionalInfoDto $additionalInfo;
    public string $inquiryStatus;
    public InquiryReasonDto $inquiryReason;
    public string $inquiryRequestId;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $virtualAccountName,
        string $virtualAccountEmail,
        string $virtualAccountPhone,
        TotalAmount $totalAmount,
        string $virtualAccountTrxType,
        string $expiredDate,
        InquiryRequestAdditionalInfoDto $additionalInfo,
        string $inquiryStatus,
        InquiryReasonDto $inquiryReason,
        string $inquiryRequestId
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->virtualAccountPhone = $virtualAccountPhone;
        $this->totalAmount = $totalAmount;
        $this->virtualAccountTrxType = $virtualAccountTrxType;
        $this->expiredDate = $expiredDate;
        $this->additionalInfo = $additionalInfo;
        $this->inquiryStatus = $inquiryStatus;
        $this->inquiryReason = $inquiryReason;
        $this->inquiryRequestId = $inquiryRequestId;
    }
}