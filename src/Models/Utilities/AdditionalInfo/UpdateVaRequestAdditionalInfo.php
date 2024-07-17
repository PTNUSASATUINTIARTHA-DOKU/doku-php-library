<?php
namespace Doku\Snap\Models\Utilities\AdditionalInfo;

use Doku\Snap\Models\Utilities\VirtualAccountConfig\UpdateVaVirtualAccountConfig;

class UpdateVaRequestAdditionalInfo
{
    public ?string $channel;
    public ?UpdateVaVirtualAccountConfig $virtualAccountConfig;

    /**
     * AdditionalInfo constructor
     * @param string $channel The channel for the request
     * @param UpdateVaVirtualAccountConfig $reusableStatus The reusable status configuration
     */
    public function __construct(?string $channel, ?UpdateVaVirtualAccountConfig $virtualAccountConfig)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
    }
}