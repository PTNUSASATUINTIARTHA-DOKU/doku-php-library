<?php
namespace Doku\Snap\Models\AdditionalInfo;
class DeleteVaRequestAdditionalInfo
{
    public ?string $channel;

    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}