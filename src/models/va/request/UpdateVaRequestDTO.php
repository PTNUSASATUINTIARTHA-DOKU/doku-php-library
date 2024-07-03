<?php
/**
 * Class UpdateVaRequestDTO
 * Represents the data for updating a virtual account.
 */
class UpdateVaRequestDTO extends VaRequestDTO
{
    public UpdateVaRequestAdditionalInfoDTO $additionalInfo;

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $virtualAccountName,
        ?string $virtualAccountEmail,
        ?string $virtualAccountPhone,
        ?string $trxId,
        TotalAmount $totalAmount,
        UpdateVaRequestAdditionalInfoDTO $updateVaAdditionalInfoDTO,
        ?string $virtualAccountTrxType,
        ?string $expiredDate
    ) {
        parent::__construct(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo,
            $virtualAccountName,
            $virtualAccountEmail,
            $virtualAccountPhone,
            $trxId,
            $totalAmount,
            $updateVaAdditionalInfoDTO,
            $virtualAccountTrxType,
            $expiredDate
        );
    }
    public function validateUpdateVaRequestDto(): bool
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
        $status &= $this->validateStatus();
        $status &= $this->validateVirtualAccountTrxType();
        $status &= $this->validateExpiredDate();

        return true;
    }

    public function validatePartnerServiceId(): bool
    {
        return !is_null($this->partnerServiceId)
            && is_string($this->partnerServiceId)
            && strlen($this->partnerServiceId) <= 20
            && preg_match('/^\d+$/', $this->partnerServiceId);
    }

    public function validateCustomerNo(): bool
    {
        $isValid = !is_null($this->customerNo)
            && is_string($this->customerNo)
            && strlen($this->customerNo) === 8
            && preg_match('/^\s{0,7}\d{1,8}$/', $this->customerNo);

        if ($isValid) {
            return $this->validateVirtualAccountNo();
        }

        return false;
    }

    public function validateVirtualAccountNo(): bool
    {
        return !is_null($this->virtualAccountNo)
            && is_string($this->virtualAccountNo)
            && $this->virtualAccountNo === $this->partnerServiceId . $this->customerNo;
    }

    public function validateVirtualAccountName(): bool
    {
        return !is_null($this->virtualAccountName)
            && is_string($this->virtualAccountName)
            && strlen($this->virtualAccountName) >= 1
            && strlen($this->virtualAccountName) <= 255
            && preg_match('/^[a-zA-Z0-9\.\-\/\,+\=_\:\'\@\% ]+$/', $this->virtualAccountName);
    }

    public function validateVirtualAccountEmail(): bool
    {
        if (is_null($this->virtualAccountEmail)) {
            return true;
        }

        return is_string($this->virtualAccountEmail)
            && strlen($this->virtualAccountEmail) >= 1
            && strlen($this->virtualAccountEmail) <= 255;
    }

    public function validateVirtualAccountPhone(): bool
    {
        if (is_null($this->virtualAccountPhone)) {
            return true;
        }

        return is_string($this->virtualAccountPhone)
            && strlen($this->virtualAccountPhone) >= 9
            && strlen($this->virtualAccountPhone) <= 30
            && preg_match('/^62/', $this->virtualAccountPhone);
    }

    public function validateTrxId(): bool
    {
        return !is_null($this->trxId)
            && is_string($this->trxId)
            && strlen($this->trxId) >= 1
            && strlen($this->trxId) <= 64;
    }

    public function validateValue(): bool
    {
        $value = $this->totalAmount->value;
        $pattern = '/^(0|[1-9]\d{0,15})(\.\d{2})?$/';

        return !is_null($value)
            && is_string($value)
            && strlen($value) >= 4
            && strlen($value) <= 19
            && preg_match($pattern, $value);
    }

    public function validateCurrency(): bool
    {
        $currency = $this->totalAmount->currency;

        if (is_null($currency)) {
            return true;
        }

        return is_string($currency)
            && strlen($currency) === 3
            && ($currency === 'IDR' || $currency === null);
    }

    public function validateChannel(): bool
    {
        $validChannels = VIRTUAL_ACCOUNT_CHANNELS;
        $channel = $this->additionalInfo->channel;

        return !is_null($channel)
            && is_string($channel)
            && strlen($channel) >= 1
            && strlen($channel) <= 30
            && in_array(strtoupper($channel), $validChannels);
    }

    public function validateStatus(): bool
    {
        $reusableStatus = $this->additionalInfo->virtualAccountConfig->status;

        if (is_null($reusableStatus)) {
            return true;
        }

        return is_bool($reusableStatus);
    }

    public function validateVirtualAccountTrxType(): bool
    {
        if (is_null($this->virtualAccountTrxType)
            || !is_string($this->virtualAccountTrxType)
            || strlen($this->virtualAccountTrxType) !== 1
            || !in_array($this->virtualAccountTrxType, ['1', '2'])
        ) {
            return false;
        }

        if ($this->virtualAccountTrxType === '2'
            && ($this->totalAmount->value !== 0 || $this->totalAmount->currency !== 'IDR')
        ) {
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

        return $dateTime !== false;
    }
}