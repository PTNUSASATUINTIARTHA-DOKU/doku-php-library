<?php

class CheckStatusVaRequestDTO
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $inquiryRequestId;
    public string $paymentRequestId;
    public string $additionalInfo;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $inquiryRequestId,
        string $paymentRequestId,
        string $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->inquiryRequestId = $inquiryRequestId;
        $this->paymentRequestId = $paymentRequestId;
        $this->additionalInfo = $additionalInfo;
    }

    public function validateCheckStatusVaRequestDto(): bool
    {
        $status = true;
        $status &= $this->validatePartnerServiceId();
        $status &= $this->validateCustomerNo();
        $status &= $this->validateVirtualAccountNo();
        $status &= $this->validateInquiryRequestId();
        $status &= $this->validatePaymentRequestId();
        $status &= $this->validateAdditionalInfo();

        return true;
    }

    private function validatePartnerServiceId(): bool
    {
        return !is_null($this->partnerServiceId)
            && is_string($this->partnerServiceId)
            && strlen($this->partnerServiceId) <= 20
            && preg_match('/^\d+$/', $this->partnerServiceId);
    }

    private function validateCustomerNo(): bool
    {
        return !is_null($this->customerNo)
            && is_string($this->customerNo)
            && strlen($this->customerNo) === 8
            && preg_match('/^\s{0,7}\d{1,8}$/', $this->customerNo);
    }

    private function validateVirtualAccountNo(): bool
    {
        return !is_null($this->virtualAccountNo)
            && is_string($this->virtualAccountNo)
            && $this->virtualAccountNo === $this->partnerServiceId . $this->customerNo;
    }

    private function validateInquiryRequestId(): bool
    {
        return !is_null($this->inquiryRequestId) && is_string($this->inquiryRequestId);
    }

    private function validatePaymentRequestId(): bool
    {
        return !is_null($this->paymentRequestId) && is_string($this->paymentRequestId);
    }

    private function validateAdditionalInfo(): bool
    {
        // You may want to add more specific validation for additionalInfo
        return true;
    }
}