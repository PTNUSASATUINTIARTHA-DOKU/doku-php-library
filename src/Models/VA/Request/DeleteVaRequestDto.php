<?php
namespace Doku\Snap\Models\VA\Request;
use Doku\Snap\Models\AdditionalInfo\DeleteVaRequestAdditionalInfo;
use Doku\Snap\Commons\VaChannels;
use InvalidArgumentException;
class DeleteVaRequestDto
{
    public ?string $partnerServiceId;
    public ?string $customerNo;
    public ?string $virtualAccountNo;
    public ?string $trxId;
    public ?DeleteVaRequestAdditionalInfo $additionalInfo;

    public function __construct(
        ?string $partnerServiceId,
        ?string $customerNo,
        ?string $virtualAccountNo,
        ?string $trxId,
        ?DeleteVaRequestAdditionalInfo $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->trxId = $trxId;
        $this->additionalInfo = $additionalInfo;
    }

    public function generateJSONBody(): string
    {
        $payload = array(
            'partnerServiceId' => $this->partnerServiceId,
            'customerNo' => $this->customerNo,
            'virtualAccountNo' => $this->virtualAccountNo,
            'trxId' => $this->trxId,
            'additionalInfo' => array(
                'channel'=> $this->additionalInfo->channel
            )
        );
        return json_encode($payload);
    }
    public function validateDeleteVaRequestDto(): void
    {
        $this->validatePartnerServiceId();
        $this->validateCustomerNo();
        $this->validateVirtualAccountNo();
        $this->validateTrxId();
        $this->validateAdditionalInfo();
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

    private function validateTrxId(): bool
    {
        if (is_null($this->trxId)) {
            throw new InvalidArgumentException("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        }
        if (!is_string($this->trxId)) {
            throw new InvalidArgumentException("trxId must be a string. Ensure that trxId is enclosed in quotes. Example: '23219829713'.");
        }
        $length = strlen($this->trxId);
        if ($length < 1) {
            throw new InvalidArgumentException("trxId must be at least 1 character long. Ensure that trxId is not empty. Example: '23219829713'.");
        }
        if ($length > 64) {
            throw new InvalidArgumentException("trxId must be 64 characters or fewer. Ensure that trxId is no longer than 64 characters. Example: '23219829713'.");
        }
        return true;
    }

    private function validateAdditionalInfo(): bool
    {
        if (!($this->additionalInfo instanceof DeleteVaRequestAdditionalInfo)) {
            throw new InvalidArgumentException("additionalInfo must be an instance of DeleteVaRequestAdditionalInfoDto.");
        }
        $this->validateChannel();
        return true;
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

    private function isValidChannel(string $channel): bool
    {
        return in_array($channel, VaChannels::VIRTUAL_ACCOUNT_CHANNELSS);
    }
}