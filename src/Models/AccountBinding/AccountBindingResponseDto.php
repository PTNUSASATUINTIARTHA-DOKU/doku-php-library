<?php
namespace Doku\Snap\Models\AccountBinding;
use Doku\Snap\Models\Utilities\AdditionalInfo\AccountBindingAdditionalInfoResponseDto;
class AccountBindingResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $referenceNo;
    public ?string $redirectUrl;
    public ?AccountBindingAdditionalInfoResponseDto $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $referenceNo,
        ?string $redirectUrl,
        ?AccountBindingAdditionalInfoResponseDto $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->referenceNo = $referenceNo;
        $this->redirectUrl = $redirectUrl;
        $this->additionalInfo = $additionalInfo;
    }
}