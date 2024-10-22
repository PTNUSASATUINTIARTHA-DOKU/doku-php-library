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

    public function validatePaymentRequestDto(): void
    {
        if (empty($this->partnerReferenceNo)) {
            throw new \InvalidArgumentException("Partner Reference Number is required");
        }

        // Cek channel
        if ($this->additionalInfo->channel === 'DIRECT_DEBIT_BRI_SNAP') {
            if (empty($this->chargeToken)) {
                throw new \InvalidArgumentException("Invalid mandatory field chargeToken");
            }
            if (strlen($this->chargeToken) > 32) {
                throw new \InvalidArgumentException("chargeToken must be at most 32 characters long");
            }
        } elseif (!in_array($this->additionalInfo->channel, ['EMONEY_OVO_SNAP'])) {
            throw new \InvalidArgumentException('Invalid channel');
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
            'lineItems' => $this->additionalInfo->lineItems
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