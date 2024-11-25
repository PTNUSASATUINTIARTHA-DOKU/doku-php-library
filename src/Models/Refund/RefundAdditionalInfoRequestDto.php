<?php

namespace Doku\Snap\Models\Refund;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;

class RefundAdditionalInfoRequestDto
{
    public $channel;
    public Origin $origin;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
        $this->origin = new Origin();
    }

    public function validate()
    {
        if (empty($this->channel)) {
            throw new \InvalidArgumentException("channel is required");
        }
    }

    public function generateJSONBody(): array
    {
        return [
            'channel' => $this->channel,
            'origin' => $this->origin->toArray()
        ];
    }
}