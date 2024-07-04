<?php
/**
 * Class UpdateVaAdditionalInfoDto
 * Represents additional information for updating a virtual account.
 */
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