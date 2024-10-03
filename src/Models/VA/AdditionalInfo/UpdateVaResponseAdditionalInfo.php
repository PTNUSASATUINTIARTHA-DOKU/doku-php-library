<?php
namespace Doku\Snap\Models\VA\AdditionalInfo;
class UpdateVaResponseAdditionalInfo
{
    public ?string $channel;

    /**
     * AdditionalInfo constructor
     * @param string $channel The channel for the request
     */
    public function __construct(?string $channel)
    {
        $this->channel = $channel;
    }
}