<?php
/**
 * Class AdditionalInfo
 * Represents additional information for the virtual account request
 */
class CreateVaRequestAdditionalInfo
{
    public ?string $channel;
    public ?CreateVaVirtualAccountConfig $virtualAccountConfig;

    /**
     * AdditionalInfo constructor
     * @param string $channel The channel for the request
     * @param CreateVaVirtualAccountConfig $reusableStatus The reusable status configuration
     */
    public function __construct(?string $channel, CreateVaVirtualAccountConfig $virtualAccountConfig)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
    }
}