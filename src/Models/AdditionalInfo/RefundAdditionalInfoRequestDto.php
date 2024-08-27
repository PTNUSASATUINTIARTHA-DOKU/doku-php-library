<?php

namespace Doku\Snap\Models\AdditionalInfo;

class RefundAdditionalInfoRequestDto
{
    public $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
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
            'channel' => $this->channel
        ];
    }
}