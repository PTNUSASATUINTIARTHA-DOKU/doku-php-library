<?php
namespace Doku\Snap\Models\Payment;
class PaymentResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $webRedirectUrl;
    public ?string $partnerReferenceNo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $webRedirectUrl,
        ?string $partnerReferenceNo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->webRedirectUrl = $webRedirectUrl;
        $this->partnerReferenceNo = $partnerReferenceNo;
    }
}