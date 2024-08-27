<?php

namespace Doku\Snap\Models\BalanceInquiry;

class BalanceInquiryResponseDto
{
    public string $responseCode;
    public string $responseMessage;
    public array $accountInfos;

    public function __construct(string $responseCode, string $responseMessage, array $accountInfos)
    {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->accountInfos = $accountInfos;
    }
}