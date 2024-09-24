<?php
namespace Doku\Snap\Models\VA\Request;
use InvalidArgumentException;
class CheckStatusVaRequestDto
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $inquiryRequestId;
    public ?string $paymentRequestId;
    public ?string $additionalInfo;

    
    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $inquiryRequestId,
        ?string $paymentRequestId,
        ?string $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->inquiryRequestId = $inquiryRequestId;
        $this->paymentRequestId = $paymentRequestId;
        $this->additionalInfo = $additionalInfo;
    }

    public function generateJSONBody(): string
    {
        $payload = array(
            'partnerServiceId' => $this->partnerServiceId,
            'customerNo' => $this->customerNo,
            'virtualAccountNo' => $this->virtualAccountNo,
            'inquiryRequestId' => $this->inquiryRequestId,
            'paymentRequestId' => $this->paymentRequestId,
            'additionalInfo' => $this->additionalInfo
        );
        return json_encode($payload);
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

        return $status;
    }

    private function validatePartnerServiceId(): bool
    {
        if (is_null($this->partnerServiceId)) {
            throw new InvalidArgumentException("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        }
        if (!is_string($this->partnerServiceId)) {
            throw new InvalidArgumentException("partnerServiceId must be a string. Ensure that partnerServiceId is enclosed in quotes. Example: ' 888994'.");
        }
        if (strlen($this->partnerServiceId) !== 8) {
            throw new InvalidArgumentException("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        }
        if (!preg_match('/^\s{0,7}\d{1,8}$/', $this->partnerServiceId)) {
            throw new InvalidArgumentException("partnerServiceId must consist of up to 7 spaces followed by 1 to 8 digits. Make sure partnerServiceId follows this format. Example: ' 888994' (2 spaces and 6 digits).");
        }
        return true;
    }

    private function validateCustomerNo(): bool
    {
        if (is_null($this->customerNo)) {
            throw new InvalidArgumentException("customerNo cannot be null.");
        }
        if (!is_string($this->customerNo)) {
            throw new InvalidArgumentException("customerNo must be a string. Ensure that customerNo is enclosed in quotes. Example: '00000000000000000001'.");
        }
        if (strlen($this->customerNo) > 20) {
            throw new InvalidArgumentException("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        }
        if (!preg_match('/^[0-9]*$/', $this->customerNo)) {
            throw new InvalidArgumentException("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        }
        return true;
    }

    private function validateVirtualAccountNo(): bool
    {
        if (is_null($this->virtualAccountNo)) {
            throw new InvalidArgumentException("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        }
        if (!is_string($this->virtualAccountNo)) {
            throw new InvalidArgumentException("virtualAccountNo must be a string. Ensure that virtualAccountNo is enclosed in quotes. Example: ' 88899400000000000000000001'.");
        }
        if ($this->partnerServiceId && $this->customerNo) {
            $expectedVirtualAccountNo = $this->partnerServiceId . $this->customerNo;
            if ($this->virtualAccountNo !== $expectedVirtualAccountNo) {
                throw new InvalidArgumentException("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
            }
        }
        return true;
    }

    private function validateInquiryRequestId(): bool
    {
        if (!is_null($this->inquiryRequestId)) {
            if (!is_string($this->inquiryRequestId)) {
                throw new InvalidArgumentException("inquiryRequestId must be a string.");
            }
            if (strlen($this->inquiryRequestId) > 128) {
                throw new InvalidArgumentException("inquiryRequestId must be 128 characters or fewer.");
            }
        }
        return true;
    }

    private function validatePaymentRequestId(): bool
    {
        if (!is_null($this->paymentRequestId)) {
            if (!is_string($this->paymentRequestId)) {
                throw new InvalidArgumentException("paymentRequestId must be a string.");
            }
            if (strlen($this->paymentRequestId) > 128) {
                throw new InvalidArgumentException("paymentRequestId must be 128 characters or fewer.");
            }
        }
        return true;
    }

    private function validateAdditionalInfo(): bool
    {
        // No specific validation for additionalInfo in example
        return true;
    }
}
