<?php
namespace Doku\Snap\Models;
class DeleteVaRequestAdditionalInfo
{
    public ?string $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}