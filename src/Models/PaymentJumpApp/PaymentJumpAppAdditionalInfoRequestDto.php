<?php
namespace Doku\Snap\Models\PaymentJumpApp;
class PaymentJumpAppAdditionalInfoRequestDto
{
    public ?string $channel;
    // Dana
    public ?string $orderTitle;
    // Shopeepay
    public ?string $metadata;

    public function __construct(
        ?string $channel,
        ?string $orderTitle,
        ?string $metadata
    ) {
        $this->channel = $channel;
        $this->orderTitle = $orderTitle;
        $this->metadata = $metadata;
    }
}