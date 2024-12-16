<?php
namespace Doku\Snap\Models\PaymentJumpApp;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;

class PaymentJumpAppAdditionalInfoRequestDto
{
    public ?string $channel;
    public ?string $orderTitle;
    public ?string $metadata;
    public Origin $origin;
    public ?string $supportDeepLinkCheckoutUrl;

    public function __construct(
        ?string $channel,
        ?string $orderTitle,
        ?string $metadata = null,
        ?string $supportDeepLinkCheckoutUrl = null
    ) {
        $this->channel = $channel;
        $this->orderTitle = $orderTitle;
        $this->metadata = $metadata;
        $this->origin = new Origin();
        $this->supportDeepLinkCheckoutUrl = $supportDeepLinkCheckoutUrl;
    }

    // Magic method __serialize untuk menyaring null values
    public function __serialize(): array
    {
        // Menyaring properti yang bernilai null
        return array_filter(get_object_vars($this), function ($value) {
            return !is_null($value);  // Menghapus nilai null
        });
    }
}