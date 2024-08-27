<?php

namespace Doku\Snap\Models\CheckStatus;
use Doku\Snap\Models\TotalAmount\TotalAmount;

class RefundHistoryDto
{
    public string $refundNo;
    public string $partnerReferenceNo;
    public TotalAmount $refundAmount;
    public string $refundStatus;
    public string $refundDate;
    public string $reason;

    public function __construct(
        string $refundNo,
        string $partnerReferenceNo,
        TotalAmount $refundAmount,
        string $refundStatus,
        string $refundDate,
        string $reason
    ) {
        $this->refundNo = $refundNo;
        $this->partnerReferenceNo = $partnerReferenceNo;
        $this->refundAmount = $refundAmount;
        $this->refundStatus = $refundStatus;
        $this->refundDate = $refundDate;
        $this->reason = $reason;
    }
}