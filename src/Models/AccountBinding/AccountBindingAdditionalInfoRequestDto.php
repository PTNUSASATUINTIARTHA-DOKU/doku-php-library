<?php
namespace Doku\Snap\Models\AccountBinding;
class AccountBindingAdditionalInfoRequestDto
{
    public ?string $channel;
    public ?string $custIdMerchant;
    public ?string $customerName;
    public ?string $email;
    public ?string $idCard;
    public ?string $country;
    public ?string $address;
    public ?string $dateOfBirth;
    public ?string $successRegistrationUrl;
    public ?string $failedRegistrationUrl;
    public ?string $deviceModel;
    public ?string $osType;
    public ?string $channelId;

    public function __construct(
        ?string $channel,
        ?string $custIdMerchant,
        ?string $customerName,
        ?string $email,
        ?string $idCard,
        ?string $country,
        ?string $address,
        ?string $dateOfBirth,
        ?string $successRegistrationUrl,
        ?string $failedRegistrationUrl,
        ?string $deviceModel,
        ?string $osType,
        ?string $channelId
    ) {
        $this->channel = $channel;
        $this->custIdMerchant = $custIdMerchant;
        $this->customerName = $customerName;
        $this->email = $email;
        $this->idCard = $idCard;
        $this->country = $country;
        $this->address = $address;
        $this->dateOfBirth = $dateOfBirth;
        $this->successRegistrationUrl = $successRegistrationUrl;
        $this->failedRegistrationUrl = $failedRegistrationUrl;
        $this->deviceModel = $deviceModel;
        $this->osType = $osType;
        $this->channelId = $channelId;
    }

    public function validate(): void
    {
        if (!in_array($this->channel, ['Mandiri', 'BRI', 'CIMB', 'Allo', 'OVO'])) {
            throw new \InvalidArgumentException("Invalid channel");
        }
        if (empty($this->custIdMerchant)) {
            throw new \InvalidArgumentException("Customer ID Merchant is required");
        }
        if (empty($this->customerName)) {
            throw new \InvalidArgumentException("Customer Name is required");
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }
        if (!preg_match("/^\d{8}$/", $this->dateOfBirth)) {
            throw new \InvalidArgumentException("Invalid date of birth format. Use YYYYMMDD");
        }
    }
}