<?php
namespace Doku\Snap\Models\PaymentJumpApp;
class PaymentJumpAppResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $webRedirectUrl;
    public ?string $referenceNo;
    public ?array $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $webRedirectUrl,
        ?string $referenceNo,
        ?array $additionalInfo 
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
            "additionalInfo"=> $this->additionalInfo,
        );
        if (!empty($this->referenceNo)) {
            $payload['referenceNo'] = $this->referenceNo;
        }
        return json_encode($payload);
    }
}