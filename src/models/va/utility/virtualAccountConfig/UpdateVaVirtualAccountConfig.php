<?php

/**
 * Class UpdateVaVirtualAccountConfig
 * Represents the configuration for a virtual account.
 */
class UpdateVaVirtualAccountConfig extends VirtualAccountConfig
{
    public ?string $status;

    /**
     * UpdateVaVirtualAccountConfig constructor.
     * @param string $status The status of the virtual account.
     */
    public function __construct(?string $status)
    {
        $this->status = $status;
    }
}