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

    public function __construct(
        ?string $partnerReferenceNo,
        ?TotalAmount $amount,
        ?array $payOptionDetails,
        ?PaymentAdditionalInfoRequestDto $additionalInfo,
        ?string $feeType
    ) {
        $this->partnerReferenceNo = $partnerReferenceNo;
        $this->amount = $amount;
        $this->payOptionDetails = $payOptionDetails;
        $this->additionalInfo = $additionalInfo;
        $this->feeType = $feeType;
    }

    public function validatePaymentRequestDto(): void
    {
        if (empty($this->partnerReferenceNo)) {
            throw new \InvalidArgumentException("Partner Reference Number is required");
        }
        // if (!in_array($this->additionalInfo->channel, ['EMONEY_OVO_SNAP'])) {
        //     throw new \InvalidArgumentException('Invalid channel');
        // }
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
            'additionalInfo' => $additionalInfoArr
        ]);
    }
}