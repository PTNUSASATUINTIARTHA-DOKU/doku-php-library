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
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'originalPartnerReferenceNo is required'
            ];
        }
        
        if (empty($this->refundAmount)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'refundAmount is required'
            ];
        }
        if (empty($this->refundAmount->value)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'refundAmount.value is required'
            ];
        }
        if (empty($this->refundAmount->currency)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'refundAmount.currency is required'
            ];
        }
        if (empty($this->additionalInfo->channel)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.channel is required'
            ];
        }
        if (empty($this->partnerRefundNo)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'partnerRefundNo is required'
            ];
        }
        if (!in_array($this->additionalInfo->channel, ['EMONEY_SHOPEE_PAY_SNAP', 'EMONEY_DANA_SNAP'])) {
            $length = strlen($this->partnerRefundNo); 
            if ($length > 12) {
                return [
                    'responseCode' => '4000701',
                    'responseMessage' => 'partnerRefundNo max length is 12'
                ];
            }
        }else{
            if ($length > 64) {
                return [
                    'responseCode' => '4000701',
                    'responseMessage' => 'partnerRefundNo max length is 64'
                ];
            }
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