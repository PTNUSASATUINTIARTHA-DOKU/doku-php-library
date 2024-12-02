<?php
namespace Doku\Snap\Models\PaymentJumpApp;
class PaymentJumpAppResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $webRedirectUrl;
    public ?string $referenceNo;
    public ?PaymentJumpAppAdditionalInfoResponseDto $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $webRedirectUrl,
        ?string $referenceNo,
        ?string $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->webRedirectUrl = $webRedirectUrl;
        $this->referenceNo = $referenceNo;
        $this->additionalInfo = $additionalInfo;
    }

    public function generateJSONBody(): string
    {
        $payload = array(
            "responseCode" => $this->responseCode,
            "responseMessage" => $this->responseMessage,
            "webRedirectUrl" => $this->webRedirectUrl,
            "referenceNo" => $this->referenceNo,
            "additionalInfo"=> $this->referenceNo,
        );
        return json_encode($payload);
    }
}