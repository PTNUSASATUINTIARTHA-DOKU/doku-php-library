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
    public ?string $supportDeepLinkCheckoutUrl;

    public function __construct(
        ?string $channel,
        ?string $orderTitle,
        ?string $metadata,
        ?string $supportDeepLinkCheckoutUrl
    ) {
        $this->channel = $channel;
        $this->orderTitle = $orderTitle;
        $this->metadata = $metadata;
        $this->origin = new Origin();
        $this->supportDeepLinkCheckoutUrl = $supportDeepLinkCheckoutUrl;
    }
}