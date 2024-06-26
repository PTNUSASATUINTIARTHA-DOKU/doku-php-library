<?php
/**
 * Class AdditionalInfo
 * Represents additional information for the virtual account request
 */
class AdditionalInfo
{
    public ?string $channel;
    public VirtualAccountConfig $virtualAccountConfig;

    /**
     * AdditionalInfo constructor
     * @param string $channel The channel for the request
     * @param VirtualAccountConfig $reusableStatus The reusable status configuration
     */
    public function __construct(?string $channel, VirtualAccountConfig $reusableStatus)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $reusableStatus;
    }
}