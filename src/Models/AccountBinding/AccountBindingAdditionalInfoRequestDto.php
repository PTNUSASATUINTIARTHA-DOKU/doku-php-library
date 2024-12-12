<?php
namespace Doku\Snap\Models\AccountBinding;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;
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
    public ?Origin $origin;

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
        $this->origin = new Origin();
    }

    public function validate()
    {
        if (empty($this->channel)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.channel is required'
            ];
        }
        if (empty($this->custIdMerchant)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.custIdMerchant is required'
            ];
        }
        if (empty($this->successRegistrationUrl)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.successRegistrationUrl is required'
            ];
        }
        if (empty($this->failedRegistrationUrl)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.failedRegistrationUrl is required'
            ];
        }
      
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'Invalid email format'
            ];
        }
       
    }
}