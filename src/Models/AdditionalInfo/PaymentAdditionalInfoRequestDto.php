<?php
namespace Doku\Snap\Models\AdditionalInfo;

class PaymentAdditionalInfoRequestDto
{
    public ?string $channel;
    public ?string $remarks;
    public ?string $successPaymentUrl;
    public ?string $failedPaymentUrl;
    public ?array $lineItems;

    public function __construct(
        ?string $channel,
        ?string $remarks,
        ?string $successPaymentUrl,
        ?string $failedPaymentUrl,
        ?array $lineItems
    ) {
        $this->channel = $channel;
        $this->remarks = $remarks;
        $this->successPaymentUrl = $successPaymentUrl;
        $this->failedPaymentUrl = $failedPaymentUrl;
        $this->lineItems = $lineItems;
    }
}