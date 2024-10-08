<?php
namespace Doku\Snap\Models\PaymentJumpApp;
class PaymentJumpAppResponseDto
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

    public function generateJSONBody(): string
    {
        $payload = array(
            "responseCode" => $this->responseCode,
            "responseMessage" => $this->responseMessage,
            "webRedirectUrl" => $this->webRedirectUrl,
            "partnerReferenceNo" => $this->partnerReferenceNo
        );
        return json_encode($payload);
    }
}