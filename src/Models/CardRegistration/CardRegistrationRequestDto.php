<?php
namespace Doku\Snap\Models\CardRegistration;
class CardRegistrationRequestDto
{
    public CardRegistrationCardDataRequestDto|string $cardData;
    public ?string $custIdMerchant;
    public ?string $phoneNo;
    public ?CardRegistrationAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        ?CardRegistrationCardDataRequestDto $cardData,
        ?string $custIdMerchant,
        ?string $phoneNo,
        ?CardRegistrationAdditionalInfoRequestDto $additionalInfo
    ) {
        $this->cardData = $cardData;
        $this->custIdMerchant = $custIdMerchant;
        $this->phoneNo = $phoneNo;
        $this->additionalInfo = $additionalInfo;
    }

    public function validate(): void
    {
        $this->cardData->validate();
        if (empty($this->custIdMerchant)) {
            throw new \InvalidArgumentException("Customer ID Merchant is required");
        }
        if (empty($this->phoneNo)) {
            throw new \InvalidArgumentException("Phone number is required");
        }
        $this->additionalInfo->validate();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'cardData' => $this->cardData,
            'custIdMerchant' => $this->custIdMerchant,
            'phoneNo' => $this->phoneNo,
            'additionalInfo' => $this->additionalInfo->generateJSONBody()
        ]);
    }
}