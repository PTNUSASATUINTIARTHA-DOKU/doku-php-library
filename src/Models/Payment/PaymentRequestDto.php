<?php
namespace Doku\Snap\Models\Payment;
use Doku\Snap\Models\Utilities\AdditionalInfo\PaymentAdditionalInfoRequestDto;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
class PaymentRequestDto
{
    public ?string $partnerReferenceNo;
    public ?TotalAmount $amount;
    public ?array $payOptionDetails;
    public ?PaymentAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        ?string $partnerReferenceNo,
        ?TotalAmount $amount,
        ?array $payOptionDetails,
        ?PaymentAdditionalInfoRequestDto $additionalInfo
    ) {
        $this->partnerReferenceNo = $partnerReferenceNo;
        $this->amount = $amount;
        $this->payOptionDetails = $payOptionDetails;
        $this->additionalInfo = $additionalInfo;
    }

    public function validatePaymentRequestDto(): void
    {
        if (empty($this->partnerReferenceNo)) {
            throw new \InvalidArgumentException("Partner Reference Number is required");
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
            'additionalInfo' => $additionalInfoArr
        ]);
    }
}