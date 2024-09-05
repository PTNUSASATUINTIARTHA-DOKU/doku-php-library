<?php

namespace Doku\Snap\Models\NotifyPayment;

use Doku\Snap\Models\TotalAmount\TotalAmount;

class NotifyPaymentDirectDebitRequestDto
{
    public string $originalPartnerReferenceNo;
    public string $originalReferenceNo;
    public string $originalExternalId;
    public string $latestTransactionStatus;
    public string $transactionStatusDesc;
    public TotalAmount $amount;
    public PaymentNotificationAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        string $originalPartnerReferenceNo,
        string $originalReferenceNo,
        string $originalExternalId,
        string $latestTransactionStatus,
        string $transactionStatusDesc,
        TotalAmount $amount,
        PaymentNotificationAdditionalInfoRequestDto $additionalInfo
    ) {
        $this->originalPartnerReferenceNo = $originalPartnerReferenceNo;
        $this->originalReferenceNo = $originalReferenceNo;
        $this->originalExternalId = $originalExternalId;
        $this->latestTransactionStatus = $latestTransactionStatus;
        $this->transactionStatusDesc = $transactionStatusDesc;
        $this->amount = $amount;
        $this->additionalInfo = $additionalInfo;
    }

    public function generateJSONBody(): array
    {
        return [
            'originalPartnerReferenceNo' => $this->originalPartnerReferenceNo,
            'originalReferenceNo' => $this->originalReferenceNo,
            'originalExternalId' => $this->originalExternalId,
            'latestTransactionStatus' => $this->latestTransactionStatus,
            'transactionStatusDesc' => $this->transactionStatusDesc,
            'amount' => $this->amount->generateJSONBody(),
            'additionalInfo' => $this->additionalInfo->generateJSONBody(),
        ];
    }
}