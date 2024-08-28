<?php
namespace Doku\Snap\Models\CardRegistration;
class CardRegistrationResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $referenceNo;
    public ?string $redirectUrl;
    public ?CardRegistrationAdditionalInfoResponseDto $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $referenceNo,
        ?string $redirectUrl,
        ?CardRegistrationAdditionalInfoResponseDto $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->referenceNo = $referenceNo;
        $this->redirectUrl = $redirectUrl;
        $this->additionalInfo = $additionalInfo;
    }
}