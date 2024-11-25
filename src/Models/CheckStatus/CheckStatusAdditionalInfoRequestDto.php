<?php

namespace Doku\Snap\Models\CheckStatus;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;

class CheckStatusAdditionalInfoRequestDto
{
    public string $deviceId;
    public string $channel;
    public Origin $origin;

    public function __construct(string $deviceId, string $channel)
    {
        $this->deviceId = $deviceId;
        $this->channel = $channel;
        $this->origin = new Origin();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'deviceId' => $this->deviceId,
            'channel' => $this->channel,
            'origin' => $this->origin->toArray()
        ]);
    }
}