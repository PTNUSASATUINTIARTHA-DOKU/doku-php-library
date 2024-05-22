<?php
/**
 * Class CreateVaRequestDTO
 * Represents the data transfer object for creating a virtual account request
 */
class CreateVaRequestDTO
{
    public string $partnerServiceId;
    public string $customerNo;
    public ?string $virtualAccountNo;
    public string $virtualAccountName;
    public string $virtualAccountEmail;
    public string $virtualAccountPhone;
    public string $trxId;
    public TotalAmount $totalAmount;
    public AdditionalInfo $additionalInfo;
    public string $virtualAccountTrxType;
    public string $expiredDate;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        ?string $virtualAccountNo,
        string $virtualAccountName,
        string $virtualAccountEmail,
        string $virtualAccountPhone,
        string $trxId,
        TotalAmount $totalAmount,
        AdditionalInfo $additionalInfo,
        string $virtualAccountTrxType,
        string $expiredDate
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->virtualAccountPhone = $virtualAccountPhone;
        $this->trxId = $trxId;
        $this->totalAmount = $totalAmount;
        $this->additionalInfo = $additionalInfo;
        $this->virtualAccountTrxType = $virtualAccountTrxType;
        $this->expiredDate = $expiredDate;
    }

    public function validateVaRequestDTO(): bool
    {
        $status = true;
        $status &= $this->validatePartnerServiceId();
        $status &= $this->validateCustomerNo();
        $status &= $this->validateVirtualAccountName();
        $status &= $this->validateVirtualAccountEmail();
        $status &= $this->validateVirtualAccountPhone();
        $status &= $this->validateTrxId();
        $status &= $this->validateValue();
        $status &= $this->validateCurrency();
        $status &= $this->validateChannel();
        $status &= $this->validateReusableStatus();
        $status &= $this->validateVirtualAccountTrxType();
        $status &= $this->validateExpiredDate();
        return $status;
    }

    public function validatePartnerServiceId(): bool
    {
        if (is_null($this->partnerServiceId) || !is_string($this->partnerServiceId) || strlen($this->partnerServiceId) > 20 || !preg_match('/^\d+$/', $this->partnerServiceId)) {
            return false;
        }
        return true;
    }
    
    public function validateCustomerNo(): bool
    {
        if (is_null($this->customerNo) || !is_string($this->customerNo) || strlen($this->customerNo) !== 8 || !preg_match('/^\s{0,7}\d{1,8}$/', $this->customerNo)) {
            return false;
        }
        return $this->validateVirtualAccountNo();
    }

    public function validateVirtualAccountNo(): bool
    {
        if (is_null($this->virtualAccountNo) || !is_string($this->virtualAccountNo) || $this->virtualAccountNo !== $this->partnerServiceId . $this->customerNo) {
            return false;
        }
        return true;
    }

    public function validateVirtualAccountName(): bool
    {
        if (is_null($this->virtualAccountName) || !is_string($this->virtualAccountName) || strlen($this->virtualAccountName) < 1 || strlen($this->virtualAccountName) > 255 || !preg_match('/^[a-zA-Z0-9\.\-\/\,+\=_\:\'\@\% ]+$/', $this->virtualAccountName)) {
            return false;
        }
        return true;
    }

    public function validateVirtualAccountEmail(): bool
    {
        if (!is_null($this->virtualAccountEmail) && (
            !is_string($this->virtualAccountEmail) ||
            strlen($this->virtualAccountEmail) < 1 ||
            strlen($this->virtualAccountEmail) > 255 ||
            !filter_var($this->virtualAccountEmail, FILTER_VALIDATE_EMAIL)
        )) {
            return false;
        }
        return true;
    }

    public function validateVirtualAccountPhone(): bool
    {
        if (!is_null($this->virtualAccountPhone) && (
            !is_string($this->virtualAccountPhone) ||
            strlen($this->virtualAccountPhone) < 9 ||
            strlen($this->virtualAccountPhone) > 30 ||
            !preg_match('/^62/', $this->virtualAccountPhone)
        )) {
            return false;
        }
        return true;
    }

    public function validateTrxId(): bool
    {
        if (is_null($this->trxId) || !is_string($this->trxId) || strlen($this->trxId) < 1 || strlen($this->trxId) > 64) {
            return false;
        }
        return true;
    }

    public function validateValue(): bool
    {
        $value = $this->totalAmount->value;
        $pattern = '/^(0|[1-9]\d{0,15})(\.\d{2})?$/';
        if (is_null($value) || !is_string($value) || strlen($value) < 4 || strlen($value) > 19 || !preg_match($pattern, $value)) {
            return false;
        }

        return true;
    }

    public function validateCurrency(): bool
    {
        $currency = $this->totalAmount->currency;
        if (!is_null($currency) && (!is_string($currency) || strlen($currency) !== 3 || ($currency !== 'IDR' && $currency !== null))) {
            return false;
        }

        return true;
    }

    public function validateChannel(): bool
    {
        $validChannels = VIRTUAL_ACCOUNT_CHANNELS;
        $channel = $this->additionalInfo->channel;
        if (is_null($channel) || !is_string($channel) || strlen($channel) < 1 || strlen($channel) > 30 || !in_array(strtolower($channel), $validChannels)) {
            return false;
    }

    return true;
    }

    public function validateReusableStatus(): bool
    {
        $reusableStatus = $this->additionalInfo->virtualAccountConfig->reusableStatus;
        if (!is_null($reusableStatus) && !is_bool($reusableStatus)) {
            return false;
    }

    return true;
    }

    public function validateVirtualAccountTrxType(): bool
    {
        if ($this->virtualAccountTrxType === null || 
            !is_string($this->virtualAccountTrxType) || 
            strlen($this->virtualAccountTrxType) !== 1 || 
            !($this->virtualAccountTrxType === '1' || $this->virtualAccountTrxType === '2')
        ) {
            return false;
        }

        if ($this->virtualAccountTrxType === '2' && ($this->totalAmount->value !== 0 || $this->totalAmount->currency !== 'IDR')) {
            return false;
        }

        return true;
    }

    public function validateExpiredDate(): bool
    {
        if ($this->expiredDate === null) {
            return false;
        }

        $dateTime = DateTime::createFromFormat(DATE_ISO8601, $this->expiredDate);

        if ($dateTime === false) {
            return false;
        }

        return true;
    }
}

