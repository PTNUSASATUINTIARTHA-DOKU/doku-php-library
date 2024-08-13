<?php
namespace Doku\Snap\Models\CardRegistration;
use Doku\Snap\Models\Utilities\AdditionalInfo\CardRegistrationAdditionalInfoRequestDto;
class CardRegistrationRequestDto
{
    public ?string $cardData;
    public ?string $custIdMerchant;
    public ?string $phoneNo;
    public ?CardRegistrationAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        ?string $cardData,
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
        if (empty($this->cardData)) {
            throw new \InvalidArgumentException("Card data is required");
        }
        if (empty($this->custIdMerchant)) {
            throw new \InvalidArgumentException("Customer ID Merchant is required");
        }
        if (empty($this->phoneNo)) {
            throw new \InvalidArgumentException("Phone number is required");
        }
        $this->additionalInfo->validate();
    }
}