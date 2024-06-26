<?php
/**
 * Class UpdateVaAdditionalInfoDto
 * Represents additional information for updating a virtual account.
 */
class UpdateVaAdditionalInfoDTO extends AdditionalInfo
{
    public ?string $channel;
    public UpdateVaVirtualAccountConfig $virtualAccountConfig;

    /**
     * UpdateVaAdditionalInfoDto constructor.
     * @param string $channel The channel for the virtual account update.
     * @param UpdateVaVirtualAccountConfig $virtualAccountConfig The virtual account configuration.
     */
    public function __construct(?string $channel, UpdateVaVirtualAccountConfig $virtualAccountConfig)
    {
        $this->channel = $channel;
        $this->virtualAccountConfig = $virtualAccountConfig;
    }
}