<?php
namespace Doku\Snap\Models\Utilities\AdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\CreateVaVirtualAccountConfig;
class CreateVaRequestAdditionalInfo
{
    public ?string $channel;
    public ?CreateVaVirtualAccountConfig $virtualAccountConfig;
    public ?Origin $origin;

    /**
     * AdditionalInfo constructor
     * @param string $channel The channel for the request
     * @param CreateVaVirtualAccountConfig $reusableStatus The reusable status configuration
     */
    public function __construct(?string $channel, CreateVaVirtualAccountConfig $virtualAccountConfig, ?Origin $origin = null)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
        $this->origin = $origin;
    }
}