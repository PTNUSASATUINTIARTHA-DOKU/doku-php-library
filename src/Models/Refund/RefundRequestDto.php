<?php

namespace Doku\Snap\Models\Refund;
use Doku\Snap\Models\TotalAmount\TotalAmount;
class RefundRequestDto
{
    public $additionalInfo;
    public $originalPartnerReferenceNo;
    public $originalExternalId;
    public $refundAmount;
    public $reason;
    public $partnerRefundNo;

    public function __construct(
        RefundAdditionalInfoRequestDto $additionalInfo,
        string $originalPartnerReferenceNo,
        string $originalExternalId,
        TotalAmount $refundAmount,
        string $reason,
        string $partnerRefundNo
    ) {
        $this->additionalInfo = $additionalInfo;
        $this->originalPartnerReferenceNo = $originalPartnerReferenceNo;
        $this->originalExternalId = $originalExternalId;
        $this->refundAmount = $refundAmount;
        $this->reason = $reason;
        $this->partnerRefundNo = $partnerRefundNo;
    }

    public function validateRefundRequestDto()
    {
        if (empty($this->originalPartnerReferenceNo)) {
            throw new \InvalidArgumentException("originalPartnerReferenceNo is required");
        }
        
        if (!$this->refundAmount instanceof TotalAmount) {
            throw new \InvalidArgumentException("refundAmount must be an instance of TotalAmount");
        }
        if (empty($this->partnerRefundNo)) {
            throw new \InvalidArgumentException("partnerRefundNo is required");
        }
        if (empty($this->partnerRefundNo)) {
            throw new \InvalidArgumentException("partnerRefundNo is required");
        }
        $length = strlen($this->partnerRefundNo);
        if ($length < 32 || $length > 64) {
            throw new \InvalidArgumentException("partnerRefundNo must be between 32 and 64 characters long");
        }
        $this->additionalInfo->validate();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'additionalInfo' => $this->additionalInfo->generateJSONBody(),
            'originalPartnerReferenceNo' => $this->originalPartnerReferenceNo,
            'originalExternalId' => $this->originalExternalId,
            'refundAmount' => $this->refundAmount->generateJSONBody(),
            'reason' => $this->reason,
            'partnerRefundNo' => $this->partnerRefundNo
        ]);
    }
}