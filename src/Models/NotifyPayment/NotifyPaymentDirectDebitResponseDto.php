<?php

namespace Doku\Snap\Models\NotifyPayment;

class NotifyPaymentDirectDebitResponseDto
{
    public string $responseCode;
    public string $approvalCode;
    public string $responseMessage;

    public function __construct(string $responseCode, string $approvalCode, string $responseMessage)
    {
        $this->responseCode = $responseCode;
        $this->approvalCode = $approvalCode;
        $this->responseMessage = $responseMessage;
    }
}