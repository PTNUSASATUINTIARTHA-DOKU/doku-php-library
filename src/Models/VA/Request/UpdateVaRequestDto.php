<?php
namespace Doku\Snap\Models\VA\Request;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Commons\VaChannels;
use DateTime;
use InvalidArgumentException;
class UpdateVaRequestDto
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $virtualAccountName;
    public ?string $virtualAccountEmail;
    public ?string $virtualAccountPhone;
    public ?string $trxId;
    public TotalAmount $totalAmount;
    public UpdateVaRequestAdditionalInfo $additionalInfo;
    public ?string $virtualAccountTrxType;
    public ?string $expiredDate;
    

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $virtualAccountName,
        ?string $virtualAccountEmail,
        ?string $virtualAccountPhone,
        ?string $trxId,
        TotalAmount $totalAmount,
        UpdateVaRequestAdditionalInfo $updateVaAdditionalInfo,
        ?string $virtualAccountTrxType,
        ?string $expiredDate
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->virtualAccountPhone = $virtualAccountPhone;
        $this->trxId = $trxId;
        $this->totalAmount = $totalAmount;
        $this->additionalInfo = $updateVaAdditionalInfo;
        $this->virtualAccountTrxType = $virtualAccountTrxType;
        $this->expiredDate = $expiredDate;
    }

    public function generateJSONBody(): string
    {
        $totalAmountArr = array(
            'value' => $this->totalAmount->value,
            'currency' => $this->totalAmount->currency
        );
        $virtualAccountConfigArr = array(
            'status' => $this->additionalInfo->virtualAccountConfig->status
        );  
        $additionalInfoArr = array(
            'channel' => $this->additionalInfo->channel,
            'virtualAccountConfig' => $virtualAccountConfigArr
        );
        $payload = array(
            'partnerServiceId' => $this->partnerServiceId,
            'customerNo' => $this->customerNo,
            'virtualAccountNo' => $this->virtualAccountNo,
            'virtualAccountName' => $this->virtualAccountName,
            'virtualAccountEmail' => $this->virtualAccountEmail,
            'virtualAccountPhone' => $this->virtualAccountPhone,
            'trxId' => $this->trxId,
            'totalAmount' => $totalAmountArr,
            'additionalInfo' => $additionalInfoArr,
            'virtualAccountTrxType' => $this->virtualAccountTrxType,
            'expiredDate' => $this->expiredDate,
        );
        return json_encode($payload);
    }

    public function validateUpdateVaRequestDto(): bool
    {
        $this->validatePartnerServiceId();
        $this->validateCustomerNo();
        $this->validateVirtualAccountNo();
        $this->validateVirtualAccountName();
        $this->validateVirtualAccountEmail();
        $this->validateVirtualAccountPhone();
        $this->validateTrxId();
        $this->validateTotalAmountCurrency();
        $this->validateTotalAmountValue();
        $this->validateAdditionalInfo();
        $this->validateVirtualAccountTrxType();
        $this->validateExpiredDate();
        return true;
    }

    private function validatePartnerServiceId(): void
    {
        if ($this->partnerServiceId === null) {
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
    }

    private function validateCustomerNo(): void
    {
        if ($this->customerNo === null) {
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
    }

    private function validateVirtualAccountNo(): void
    {
        if ($this->virtualAccountNo === null) {
            throw new InvalidArgumentException("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        }
        if (!is_string($this->virtualAccountNo)) {
            throw new InvalidArgumentException("virtualAccountNo must be a string. Ensure that virtualAccountNo is enclosed in quotes. Example: ' 88899400000000000000000001'.");
        }
        $target = $this->partnerServiceId . $this->customerNo;
        if ($this->virtualAccountNo !== $target) {
            throw new InvalidArgumentException("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        }
    }

    private function validateVirtualAccountName(): void
    {
        if ($this->virtualAccountName !== null) {
            if (!is_string($this->virtualAccountName)) {
                throw new InvalidArgumentException("virtualAccountName must be a string. Ensure that virtualAccountName is enclosed in quotes. Example: 'Toru Yamashita'.");
            }
            if (strlen($this->virtualAccountName) < 1 || strlen($this->virtualAccountName) > 255) {
                throw new InvalidArgumentException("virtualAccountName must be between 1 and 255 characters long. Ensure that virtualAccountName is not empty and no longer than 255 characters. Example: 'Toru Yamashita'.");
            }
            if (!preg_match('/^[a-zA-Z0-9.\-\/+,=_:\'@% ]*$/', $this->virtualAccountName)) {
                throw new InvalidArgumentException("virtualAccountName can only contain letters, numbers, spaces, and the following characters: .\\-/+,=_:'@%. Ensure that virtualAccountName does not contain invalid characters. Example: 'Toru.Yamashita-123'.");
            }
        }
    }

    private function validateVirtualAccountEmail(): void
    {
        if ($this->virtualAccountEmail !== null) {
            if (!is_string($this->virtualAccountEmail)) {
                throw new InvalidArgumentException("virtualAccountEmail must be a string. Ensure that virtualAccountEmail is enclosed in quotes. Example: 'toru@example.com'.");
            }
            if (strlen($this->virtualAccountEmail) < 1 || strlen($this->virtualAccountEmail) > 255) {
                throw new InvalidArgumentException("virtualAccountEmail must be between 1 and 255 characters long. Ensure that virtualAccountEmail is not empty and no longer than 255 characters. Example: 'toru@example.com'.");
            }
            if (!filter_var($this->virtualAccountEmail, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("virtualAccountEmail must be a valid email address. Example: 'toru@example.com'.");
            }
        }
    }

    private function validateVirtualAccountPhone(): void
    {
        if ($this->virtualAccountPhone !== null) {
            if (!is_string($this->virtualAccountPhone)) {
                throw new InvalidArgumentException("virtualAccountPhone must be a string. Ensure that virtualAccountPhone is enclosed in quotes. Example: '628123456789'.");
            }
            if (strlen($this->virtualAccountPhone) < 9 || strlen($this->virtualAccountPhone) > 30) {
                throw new InvalidArgumentException("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
            }
        }
    }

    private function validateTrxId(): void
    {
        if ($this->trxId === null) {
            throw new InvalidArgumentException("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        }
        if (!is_string($this->trxId)) {
            throw new InvalidArgumentException("trxId must be a string. Ensure that trxId is enclosed in quotes. Example: '23219829713'.");
        }
        if (strlen($this->trxId) < 1 || strlen($this->trxId) > 64) {
            throw new InvalidArgumentException("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        }
    }

    private function validateTotalAmountCurrency(): void
    {
        if ($this->totalAmount->currency !== "IDR") {
            throw new InvalidArgumentException("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        }
    }

    private function validateTotalAmountValue(): void
    {
        $value = $this->totalAmount->value;

        if ($value === null) {
            throw new InvalidArgumentException("totalAmount.value cannot be null.");
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException("totalAmount.value must be a string. Ensure that totalAmount.value is enclosed in quotes. Example: '11500.00'.");
        }

        if (strlen($value) < 4) {
            throw new InvalidArgumentException("totalAmount.value must be at least 4 characters long and formatted as 0.00. Ensure that totalAmount.value is at least 4 characters long and in the correct format. Example: '100.00'.");
        }

        if (strlen($value) > 19) {
            throw new InvalidArgumentException("totalAmount.value must be 19 characters or fewer and formatted as 9999999999999999.99. Ensure that totalAmount.value is no longer than 19 characters and in the correct format. Example: '9999999999999999.99'.");
        }

        if (!preg_match('/^(0|[1-9]\d{0,15})(\.\d{2})?$/', $value)) {
            throw new InvalidArgumentException("totalAmount.value must be in the format of a valid number with up to 2 decimal places. Ensure that totalAmount.value follows the correct format. Example: '11500.00'.");
        }
    }

    private function validateAdditionalInfo(): void
    {
        $this->validateChannel();
        $this->validateStatus();
        $this->validateMinMaxAmount();
    }

    private function validateChannel(): void
    {
        $channel = $this->additionalInfo->channel;

        if ($channel === null) {
            throw new InvalidArgumentException("additionalInfo.channel cannot be null.");
        }

        if (!is_string($channel)) {
            throw new InvalidArgumentException("additionalInfo.channel must be a string. Ensure that additionalInfo.channel is enclosed in quotes. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        }

        if (strlen($channel) < 1) {
            throw new InvalidArgumentException("additionalInfo.channel must be at least 1 character long. Ensure that additionalInfo.channel is not empty. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        }

        if (strlen($channel) > 30) {
            throw new InvalidArgumentException("additionalInfo.channel must be 30 characters or fewer. Ensure that additionalInfo.channel is no longer than 30 characters. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        }

        if (!$this->isValidChannel($channel)) {
            throw new InvalidArgumentException("additionalInfo.channel is not valid. Ensure that additionalInfo.channel is one of the valid channels. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        }
    }

    private function validateStatus(): void
    {
        $status = $this->additionalInfo->virtualAccountConfig->status;
        if ($status !== "ACTIVE" && $status !== "INACTIVE") {
            throw new InvalidArgumentException("status must be either 'ACTIVE' or 'INACTIVE'. Ensure that status is one of these values. Example: 'INACTIVE'.");
        }
    }

    private function validateMinMaxAmount(): void
    {
        $minAmount = $this->additionalInfo->virtualAccountConfig->minAmount;
        $maxAmount = $this->additionalInfo->virtualAccountConfig->maxAmount;
        
        if ($minAmount !== null && $maxAmount !== null) {
            if ($this->virtualAccountTrxType === "C") {
                throw new InvalidArgumentException("Only supported for virtualAccountTrxType O and V only");
            }

            if ($minAmount >= $maxAmount) {
                throw new InvalidArgumentException("maxAmount cannot be lesser than minAmount");
            }
        }
    }

    private function validateVirtualAccountTrxType(): void
    {
        if ($this->virtualAccountTrxType === null) {
            throw new InvalidArgumentException("virtualAccountTrxType cannot be null.");
        }
        if (!is_string($this->virtualAccountTrxType)) {
            throw new InvalidArgumentException("virtualAccountTrxType must be a string. Ensure that virtualAccountTrxType is enclosed in quotes. Example: 'C'.");
        }
        if (strlen($this->virtualAccountTrxType) !== 1) {
            throw new InvalidArgumentException("virtualAccountTrxType must be exactly 1 character long. Ensure that virtualAccountTrxType is either 'C', 'O', or 'V'. Example: 'C'.");
        }
        if (!in_array($this->virtualAccountTrxType, ['C', 'O', 'V'])) {
            throw new InvalidArgumentException("virtualAccountTrxType must be either 'C', 'O', or 'V'. Ensure that virtualAccountTrxType is one of these values. Example: 'C'.");
        }
    }

    private function validateExpiredDate(): void
    {
        if ($this->expiredDate !== null) {
            if (!is_string($this->expiredDate)) {
                throw new InvalidArgumentException("expiredDate must be a string. Ensure that expiredDate is enclosed in quotes.");
            }
            $dateTime = DateTime::createFromFormat(DATE_ISO8601, $this->expiredDate);
            if ($dateTime === false) {
                throw new InvalidArgumentException("expiredDate must be in ISO-8601 format. Ensure that expiredDate follows the correct format. Example: '2023-01-01T10:55:00+07:00'.");
            }
        }
    }

    private function isValidChannel(string $channel): bool
    {
        return in_array($channel, VaChannels::VIRTUAL_ACCOUNT_CHANNELSS);
    }
}
