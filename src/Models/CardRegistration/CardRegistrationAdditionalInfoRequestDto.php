<?php
namespace Doku\Snap\Models\CardRegistration;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;
class CardRegistrationAdditionalInfoRequestDto
{
    public ?string $channel;
    public ?string $customerName;
    public ?string $email;
    public ?string $idCard;
    public ?string $country;
    public ?string $address;
    public ?string $dateOfBirth;
    public ?string $successRegistrationUrl;
    public ?string $failedRegistrationUrl;
    public Origin $origin;

    public function __construct(
        ?string $channel,
        ?string $customerName,
        ?string $email,
        ?string $idCard,
        ?string $country,
        ?string $address,
        ?string $dateOfBirth,
        ?string $successRegistrationUrl,
        ?string $failedRegistrationUrl
    ) {
        $this->channel = $channel;
        $this->customerName = $customerName;
        $this->email = $email;
        $this->idCard = $idCard;
        $this->country = $country;
        $this->address = $address;
        $this->dateOfBirth = $dateOfBirth;
        $this->successRegistrationUrl = $successRegistrationUrl;
        $this->failedRegistrationUrl = $failedRegistrationUrl;
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
        if (!preg_match("/^\d{8}$/", $this->dateOfBirth)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'Invalid date of birth format. Use YYYYMMDD'
            ];
        }
    }

    public function generateJSONBody(): array
    {
        return [
            'channel' => $this->channel,
            'customerName' => $this->customerName,
            'email' => $this->email,
            'idCard' => $this->idCard,
            'country' => $this->country,
            'address' => $this->address,
            'dateOfBirth' => $this->dateOfBirth,
            'successRegistrationUrl' => $this->successRegistrationUrl,
            'failedRegistrationUrl' => $this->failedRegistrationUrl,
            'origin' => $this->origin->toArray()
        ];
    }
}

