<?php

class CheckStatusVirtualAccountData
{
    public ?CheckStatusResponsePaymentFlagReason $paymentFlagReason;
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $inquiryRequestId;
    public ?string $paymentRequestId;
    public ?string $trxId;
    public ?TotalAmount $paidAmount;
    public ?TotalAmount $billAmount;
    public ?CheckStatusResponseAdditionalInfo $additionalInfo;

    public function __construct(
        ?CheckStatusResponsePaymentFlagReason $paymentFlagReason,
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $inquiryRequestId,
        ?string $paymentRequestId,
        ?string $trxId,
        ?TotalAmount $paidAmount,
        ?TotalAmount $billAmount,
        ?CheckStatusResponseAdditionalInfo $additionalInfo
    ) {
        $this->paymentFlagReason = $paymentFlagReason;
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->inquiryRequestId = $inquiryRequestId;
        $this->paymentRequestId = $paymentRequestId;
        $this->virtualAccountNumber = $trxId;
        $this->paidAmount = $paidAmount;
        $this->billAmount = $billAmount;
        $this->additionalInfo = $additionalInfo;
    }
}