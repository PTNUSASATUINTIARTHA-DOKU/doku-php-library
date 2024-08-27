<?php

namespace Doku\Snap\Models\Refund;
use Doku\Snap\Models\TotalAmount\TotalAmount;

class RefundResponseDto
{
    public $responseCode;
    public $responseMessage;
    public $refundAmount;
    public $originalPartnerReferenceNo;
    public $originalReferenceNo;
    public $refundNo;
    public $partnerRefundNo;
    public $refundTime;

    public function __construct(
        string $responseCode,
        string $responseMessage,
        TotalAmount $refundAmount,
        string $originalPartnerReferenceNo,
        string $originalReferenceNo,
        string $refundNo,
        string $partnerRefundNo,
        string $refundTime
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->refundAmount = $refundAmount;
        $this->originalPartnerReferenceNo = $originalPartnerReferenceNo;
        $this->originalReferenceNo = $originalReferenceNo;
        $this->refundNo = $refundNo;
        $this->partnerRefundNo = $partnerRefundNo;
        $this->refundTime = $refundTime;
    }
}