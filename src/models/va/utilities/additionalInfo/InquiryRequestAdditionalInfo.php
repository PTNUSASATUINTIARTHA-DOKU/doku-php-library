<?php
namespace Doku\Snap\Models;
class DeleteVaResponseAdditionalInfo
{
    public ?string $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}