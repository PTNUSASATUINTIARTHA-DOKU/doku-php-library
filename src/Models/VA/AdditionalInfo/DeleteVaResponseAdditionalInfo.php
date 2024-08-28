<?php
namespace Doku\Snap\Models\VA\AdditionalInfo;
class DeleteVaResponseAdditionalInfo
{
    public ?string $channel;
    public ?string $virtualAccountConfig;

    public function __construct(?string $channel, ?string $virtualAccountConfig)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
    }
}