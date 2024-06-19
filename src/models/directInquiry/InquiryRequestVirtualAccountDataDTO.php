<?php

class InquiryRequestVirtualAccountDataDTO
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
    public AdditionalInfo $additionalInfo;
    public string $inquiryStatus;
    public InquiryReasonDTO $inquiryReason;
    public string $inquiryRequestId;

    /**
     * InquiryRequestVirtualAccountDataDTO constructor.
     *
     * @param string $partnerServiceId
     * @param string $customerNo
     * @param string $virtualAccountNo
     * @param string $virtualAccountName
     * @param string $virtualAccountEmail
     * @param string $virtualAccountPhone
     * @param TotalAmount $totalAmount
     * @param string $virtualAccountTrxType
     * @param string $expiredDate
     * @param AdditionalInfo $additionalInfo
     * @param string $inquiryStatus
     * @param InquiryReasonDTO $inquiryReason
     * @param string $inquiryRequestId
     */
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
        AdditionalInfo $additionalInfo,
        string $inquiryStatus,
        InquiryReasonDTO $inquiryReason,
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