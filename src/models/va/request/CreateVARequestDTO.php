<?php
namespace Doku\Snap\Models\VA\Request;

use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
use Doku\Snap\Models\Utilities\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Commons\VaChannels;
use DateTime;
use Exception;

class CreateVaRequestDto
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $virtualAccountName;
    public ?string $virtualAccountEmail;
    public ?string $virtualAccountPhone;
    public ?string $trxId;
    public TotalAmount $totalAmount;
    public CreateVaRequestAdditionalInfo $additionalInfo;
    public ?string $virtualAccountTrxType;
    public ?string $expiredDate;

    public function validateCreateVaRequestDto(): bool
    {
        $this->validatePartnerServiceId();
        $this->validateCustomerNo();
        $this->validateVirtualAccountNo();
        $this->validateVirtualAccountName();
        $this->validateVirtualAccountEmail();
        $this->validateVirtualAccountPhone();
        $this->validateTrxId();
        $this->validateTotalAmountCurrency();
        $this->validateVirtualAccountTrxType();
        $this->validateExpiredDate();
        $this->validateAdditionalInfo();
        $this->validateChannel();
        $this->validateMinMaxAmount();
        return true;
    }

    private function validatePartnerServiceId(): void
    {
        if ($this->partnerServiceId === null) {
            throw new Exception("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        }
        if (!is_string($this->partnerServiceId)) {
            throw new Exception("partnerServiceId must be a string. Ensure that partnerServiceId is enclosed in quotes. Example: ' 888994'.");
        }
        if (strlen($this->partnerServiceId) !== 8) {
            throw new Exception("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        }
        if (!preg_match('/^\s{0,7}\d{1,8}$/', $this->partnerServiceId)) {
            throw new Exception("partnerServiceId must consist of up to 7 spaces followed by 1 to 8 digits. Make sure partnerServiceId follows this format. Example: ' 888994' (2 spaces and 6 digits).");
        }
    }

    private function validateCustomerNo(): void
    {
        if ($this->customerNo === null) {
            throw new Exception("customerNo cannot be null.");
        }
        if (!is_string($this->customerNo)) {
            throw new Exception("customerNo must be a string. Ensure that customerNo is enclosed in quotes. Example: '00000000000000000001'.");
        }
        if (strlen($this->customerNo) > 20) {
            throw new Exception("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        }
        if (!preg_match('/^[0-9]*$/', $this->customerNo)) {
            throw new Exception("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        }
    }

    private function validateVirtualAccountNo(): void
    {
        if ($this->virtualAccountNo === null) {
            throw new Exception("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        }
        if (!is_string($this->virtualAccountNo)) {
            throw new Exception("virtualAccountNo must be a string. Ensure that virtualAccountNo is enclosed in quotes. Example: ' 88899400000000000000000001'.");
        }
        $target = $this->partnerServiceId . $this->customerNo;
        if ($this->virtualAccountNo !== $target) {
            throw new Exception("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        }

        if (!empty($this->partnerServiceId) && !empty($this->customerNo) && !empty($this->virtualAccountNo)) {
            $target = $this->partnerServiceId . $this->customerNo;
            if ($this->virtualAccountNo !== $target) {
                throw new Exception("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
            }
        }
    }

    private function validateVirtualAccountName(): void
    {
        if ($this->virtualAccountName === null) {
            throw new Exception("virtualAccountName cannot be null. Please provide a virtualAccountName. Example: 'Toru Yamashita'.");
        }
        if (!is_string($this->virtualAccountName)) {
            throw new Exception("virtualAccountName must be a string. Ensure that virtualAccountName is enclosed in quotes. Example: 'Toru Yamashita'.");
        }
        $length = strlen($this->virtualAccountName);
        if ($length < 1 || $length > 255) {
            throw new Exception("virtualAccountName must be between 1 and 255 characters long. Ensure that virtualAccountName is not empty and no longer than 255 characters. Example: 'Toru Yamashita'.");
        }
        if (!preg_match('/^[a-zA-Z0-9.\-\/+,=_:\'@% ]*$/', $this->virtualAccountName)) {
            throw new Exception("virtualAccountName can only contain letters, numbers, spaces, and the following characters: .\\-/+,=_:'@%. Ensure that virtualAccountName does not contain invalid characters. Example: 'Toru.Yamashita-123'.");
        }
    }

    private function validateVirtualAccountEmail(): void
    {
        if ($this->virtualAccountEmail !== null) {
            if (!is_string($this->virtualAccountEmail)) {
                throw new Exception("virtualAccountEmail must be a string. Ensure that virtualAccountEmail is enclosed in quotes. Example: 'toru@example.com'.");
            }
            $length = strlen($this->virtualAccountEmail);
            if ($length < 1 || $length > 255) {
                throw new Exception("virtualAccountEmail must be between 1 and 255 characters long. Ensure that virtualAccountEmail is not empty and no longer than 255 characters. Example: 'toru@example.com'.");
            }
            if (!filter_var($this->virtualAccountEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("virtualAccountEmail must be a valid email address. Example: 'toru@example.com'.");
            }
        }
    }

    private function validateVirtualAccountPhone(): void
    {
        if ($this->virtualAccountPhone !== null) {
            if (!is_string($this->virtualAccountPhone)) {
                throw new Exception("virtualAccountPhone must be a string. Ensure that virtualAccountPhone is enclosed in quotes. Example: '628123456789'.");
            }
            $length = strlen($this->virtualAccountPhone);
            if ($length < 9 || $length > 30) {
                throw new Exception("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
            }
        }
    }

    private function validateTrxId(): void
    {
        if ($this->trxId === null) {
            throw new Exception("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        }
        if (!is_string($this->trxId)) {
            throw new Exception("trxId must be a string. Ensure that trxId is enclosed in quotes. Example: '23219829713'.");
        }
        $length = strlen($this->trxId);
        if ($length < 1 || $length > 64) {
            throw new Exception("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        }
    }
    private function validateVirtualAccountTrxType(): void
    {
        if ($this->virtualAccountTrxType === null) {
            throw new Exception("virtualAccountTrxType cannot be null.");
        }
        if (!is_string($this->virtualAccountTrxType)) {
            throw new Exception("virtualAccountTrxType must be a string. Ensure that virtualAccountTrxType is enclosed in quotes. Example: 'C'.");
        }
        if (strlen($this->virtualAccountTrxType) !== 1) {
            throw new Exception("virtualAccountTrxType must be exactly 1 character long. Ensure that virtualAccountTrxType is either 'C', 'O', or 'V'. Example: 'C'.");
        }
        if (!in_array($this->virtualAccountTrxType, ['C', 'O', 'V'])) {
            throw new Exception("virtualAccountTrxType must be either 'C', 'O', or 'V'. Ensure that virtualAccountTrxType is one of these values. Example: 'C'.");
        }
    }

    private function validateExpiredDate(): void
    {
        if ($this->expiredDate !== null) {
            if (!is_string($this->expiredDate)) {
                throw new Exception("expiredDate must be a string. Ensure that expiredDate is enclosed in quotes.");
            }
            $dateTime = DateTime::createFromFormat(DATE_ISO8601, $this->expiredDate);
            if ($dateTime === false) {
                throw new Exception("expiredDate must be in ISO-8601 format. Ensure that expiredDate follows the correct format. Example: '2023-01-01T10:55:00+07:00'.");
            }
        }
    }

    private function validateTotalAmountCurrency(): void
    {
        if ($this->totalAmount->currency !== "IDR") {
            throw new Exception("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        }
    }

    private function validateAdditionalInfo(): void
    {
        if (isset($this->additionalInfo->virtualAccountConfig)) {
            if (!isset($this->additionalInfo->virtualAccountConfig->reusableStatus)) {
                $this->additionalInfo->virtualAccountConfig->reusableStatus = false;
            }
        }
    }

    private function validateChannel(): void
    {
        if (!$this->isValidChannel($this->additionalInfo->channel)) {
            throw new Exception("additionalInfo.channel is not valid. Ensure that additionalInfo.channel is one of the valid channels. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        }
    }

    private function validateMinMaxAmount(): void
    {
        if (isset($this->additionalInfo->virtualAccountConfig->minAmount) &&
            isset($this->additionalInfo->virtualAccountConfig->maxAmount)) {
            if ($this->virtualAccountTrxType === "C") {
                throw new Exception("Only supported for virtualAccountTrxType O and V only");
            }

            if ($this->additionalInfo->virtualAccountConfig->minAmount >= $this->additionalInfo->virtualAccountConfig->maxAmount) {
                throw new Exception("maxAmount cannot be lesser than minAmount");
            }
        }
    }

    private function isValidChannel(string $channel): bool
    {
        return in_array($channel, VaChannels::VIRTUAL_ACCOUNT_CHANNELSS);
    }
}