<?php
namespace Doku\Snap\Models\AdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig;
class CreateVaRequestAdditionalInfo
{
    public ?string $channel;
    public ?CreateVaVirtualAccountConfig $virtualAccountConfig;
    public ?Origin $origin;

    public function __construct(?string $channel, CreateVaVirtualAccountConfig $virtualAccountConfig, ?Origin $origin = null)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
        $this->origin = $origin;
    }
}