<?php
namespace Doku\Snap\Models\AccountUnbinding;
class AccountUnbindingResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $referenceNo;

    public function __construct(?string $responseCode, ?string $responseMessage, ?string $referenceNo)
    {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->referenceNo = $referenceNo;
    }
}