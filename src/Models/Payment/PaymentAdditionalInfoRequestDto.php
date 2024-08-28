<?php
namespace Doku\Snap\Models\Payment;

class PaymentAdditionalInfoRequestDto
{
    public ?string $channel;
    // Only for Allo Bank Direct Debit and CIMB Direct Debit
    public ?string $remarks;
    public ?string $successPaymentUrl;
    public ?string $failedPaymentUrl;
    // OnlyAlloBank
    public ?array $lineItems;
    // Only BRI and OVO
    public? string $paymentType;

    public function __construct(
        ?string $channel,
        ?string $remarks,
        ?string $successPaymentUrl,
        ?string $failedPaymentUrl,
        ?array $lineItems,
        ?string $paymentType
    ) {
        $this->channel = $channel;
        $this->remarks = $remarks;
        $this->successPaymentUrl = $successPaymentUrl;
        $this->failedPaymentUrl = $failedPaymentUrl;
        $this->lineItems = $lineItems;
        $this->paymentType = $paymentType;
    }
}