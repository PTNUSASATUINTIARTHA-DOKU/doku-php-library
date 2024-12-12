<?php
namespace Doku\Snap\Models\Payment;
use Doku\Snap\Models\TotalAmount\TotalAmount;
class PaymentRequestDto
{
    public ?string $partnerReferenceNo;
    public ?TotalAmount $amount;
    // Only AlloBank
    public ?array $payOptionDetails;
    public ?PaymentAdditionalInfoRequestDto $additionalInfo;
    // Only for OVO
    public ?string $feeType;
    public ?string $chargeToken;

    public function __construct(
        ?string $partnerReferenceNo,
        ?TotalAmount $amount,
        ?array $payOptionDetails,
        ?PaymentAdditionalInfoRequestDto $additionalInfo,
        ?string $feeType,
        ?string $chargeToken
    ) {
        $this->partnerReferenceNo = $partnerReferenceNo;
        $this->amount = $amount;
        $this->payOptionDetails = $payOptionDetails;
        $this->additionalInfo = $additionalInfo;
        $this->feeType = $feeType;
        $this->chargeToken = $chargeToken;
    }

    public function validatePaymentRequestDto()
    {
        if (empty($this->partnerReferenceNo)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'partnerReferenceNo is required'
            ];
        }
        if (empty($this->amount)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount is required'
            ];
        }
        if (empty($this->amount->value)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount.value is required'
            ];
        }
        if (empty($this->amount->currency)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount.currency is required'
            ];
        }
        if (empty($this->payOptionDetails)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'payOptionDetails is required'
            ];
        }
        if (empty($this->additionalInfo)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo is required'
            ];
        }
        if (empty($this->additionalInfo->channel)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.channel is required'
            ];
        }
        if (empty($this->additionalInfo->successPaymentUrl)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.successPaymentUrl is required'
            ];
        }
        if (empty($this->additionalInfo->failedPaymentUrl)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'additionalInfo.failedPaymentUrl is required'
            ];
        }
        // Cek channel

        if ($this->additionalInfo->channel === 'DIRECT_DEBIT_BRI_SNAP') {
            if (empty($this->chargeToken)) {
                return [
                    'responseCode' => '4000701',
                    'responseMessage' => 'Invalid mandatory field chargeToken'
                ];
            }
            if (strlen($this->chargeToken) > 32) {
                return [
                    'responseCode' => '4000701',
                    'responseMessage' => 'chargeToken must be at most 32 characters long'
                ];
            }
        }
    }


    public function generateJSONBody(): string
    {
        $totalAmountArr = array(
            'value' => $this->amount->value,
            'currency' => $this->amount->currency
        );
        $additionalInfoArr = array(
            'channel' => $this->additionalInfo->channel,
            'remarks' => $this->additionalInfo->remarks,
            'successPaymentUrl' => $this->additionalInfo->successPaymentUrl,
            'failedPaymentUrl' => $this->additionalInfo->failedPaymentUrl,
            'lineItems' => $this->additionalInfo->lineItems,
            'origin' => $this->additionalInfo->origin->toArray()
        );
        return json_encode([
            'partnerReferenceNo' => $this->partnerReferenceNo,
            'amount' => $totalAmountArr,
            'payOptionDetails' => $this->payOptionDetails,
            'additionalInfo' => $additionalInfoArr,
            'chargeToken' => $this->chargeToken
        ]);
    }
}