<?php
namespace Doku\Snap\Models\Utilities\AdditionalInfo;
class PaymentJumpAppAdditionalInfoRequestDto
{
    public ?string $channel;
    public ?string $orderTitle;
    public ?string $metadata;

    public function __construct(
        ?string $channel,
        ?string $orderTitle,
        ?string $metadata
    ) {
        $this->channel = $channel;
        $this->orderTitle = $orderTitle; // dana
        $this->metadata = $metadata; // shoppe pay
    }
}