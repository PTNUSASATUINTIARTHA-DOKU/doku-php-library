<?php

namespace Doku\Snap\Models\CheckStatus;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\AdditionalInfo\CheckStatusAdditionalInfoResponseDto;

class CheckStatusResponseDto
{
    public string $responseCode;
    public string $responseMessage;
    public string $originalPartnerReferenceNo;
    public string $originalReferenceNo;
    public string $approvalCode;
    public string $originalExternalId;
    public string $serviceCode;
    public string $latestTransactionStatus;
    public string $transactionStatusDesc;
    public string $originalResponseCode;
    public string $originalResponseMessage;
    public string $sessionId;
    public string $requestId;
    public array $refundHistory;
    public TotalAmount $transAmount;
    public TotalAmount $feeAmount;
    public string $paidTime;
    public CheckStatusAdditionalInfoResponseDto $additionalInfo;

    public function __construct(
        string $responseCode,
        string $responseMessage,
        string $originalPartnerReferenceNo,
        string $originalReferenceNo,
        string $approvalCode,
        string $originalExternalId,
        string $serviceCode,
        string $latestTransactionStatus,
        string $transactionStatusDesc,
        string $originalResponseCode,
        string $originalResponseMessage,
        string $sessionId,
        string $requestId,
        array $refundHistory,
        TotalAmount $transAmount,
        TotalAmount $feeAmount,
        string $paidTime,
        CheckStatusAdditionalInfoResponseDto $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->originalPartnerReferenceNo = $originalPartnerReferenceNo;
        $this->originalReferenceNo = $originalReferenceNo;
        $this->approvalCode = $approvalCode;
        $this->originalExternalId = $originalExternalId;
        $this->serviceCode = $serviceCode;
        $this->latestTransactionStatus = $latestTransactionStatus;
        $this->transactionStatusDesc = $transactionStatusDesc;
        $this->originalResponseCode = $originalResponseCode;
        $this->originalResponseMessage = $originalResponseMessage;
        $this->sessionId = $sessionId;
        $this->requestId = $requestId;
        $this->refundHistory = $refundHistory;
        $this->transAmount = $transAmount;
        $this->feeAmount = $feeAmount;
        $this->paidTime = $paidTime;
        $this->additionalInfo = $additionalInfo;
    }
}