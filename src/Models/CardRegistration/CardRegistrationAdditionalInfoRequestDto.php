<?php
namespace Doku\Snap\Models\CardRegistration;
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
    }

    public function validate(): void
    {
        if (!in_array($this->channel, ['Mandiri', 'BRI', 'CIMB', 'Allo', 'OVO'])) {
            throw new \InvalidArgumentException("Invalid channel");
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
            'failedRegistrationUrl' => $this->failedRegistrationUrl
        ];
    }
}

