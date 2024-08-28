<?php

namespace Doku\Snap\Models\CheckStatus;

class CheckStatusAdditionalInfoResponseDto
{
    public string $deviceId;
    public string $channel;
    public ?object $acquirer;

    public function __construct(string $deviceId, string $channel, ?object $acquirer = null)
    {
        $this->deviceId = $deviceId;
        $this->channel = $channel;
        $this->acquirer = $acquirer;
    }
}