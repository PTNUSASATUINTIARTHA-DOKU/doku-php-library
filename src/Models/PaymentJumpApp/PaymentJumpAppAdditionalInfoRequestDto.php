<?php
namespace Doku\Snap\Models\PaymentJumpApp;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;
class PaymentJumpAppAdditionalInfoRequestDto
{
    public ?string $channel;
    // Dana
    public ?string $orderTitle;
    // Shopeepay
    public ?string $metadata;
    public Origin $origin;

    public function __construct(
        ?string $channel,
        ?string $orderTitle,
        ?string $metadata
    ) {
        $this->channel = $channel;
        $this->orderTitle = $orderTitle;
        $this->metadata = $metadata;
        $this->origin = new Origin();
    }
}