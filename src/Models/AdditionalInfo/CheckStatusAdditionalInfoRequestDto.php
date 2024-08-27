<?php

namespace Doku\Snap\Models\AdditionalInfo;

class CheckStatusAdditionalInfoRequestDto
{
    public string $deviceId;
    public string $channel;

    public function __construct(string $deviceId, string $channel)
    {
        $this->deviceId = $deviceId;
        $this->channel = $channel;
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'deviceId' => $this->deviceId,
            'channel' => $this->channel
        ]);
    }
}