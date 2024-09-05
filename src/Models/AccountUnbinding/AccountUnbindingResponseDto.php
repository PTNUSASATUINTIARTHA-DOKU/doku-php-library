<?php
namespace Doku\Snap\Models\AccountUnbinding;
class AccountUnbindingResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $referenceNo;
    public ?string $redirectUrl; // Only for Registration Card Unbinding

    public function __construct(?string $responseCode, ?string $responseMessage, ?string $referenceNo, ?string $redirectUrl)
    {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->referenceNo = $referenceNo;
        $this->redirectUrl = $redirectUrl;
    }
}