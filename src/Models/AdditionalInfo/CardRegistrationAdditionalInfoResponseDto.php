<?php
namespace Doku\Snap\Models\AdditionalInfo;
class CardRegistrationAdditionalInfoResponseDto
{
    public ?string $custIdMerchant;
    public ?string $status;
    public ?string $authCode;

    public function __construct(?string $custIdMerchant, ?string $status, ?string $authCode)
    {
        $this->custIdMerchant = $custIdMerchant;
        $this->status = $status;
        $this->authCode = $authCode;
    }
}