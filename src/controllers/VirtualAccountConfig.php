<?php
/**
 * Class VirtualAccountConfig
 * Represents the configuration for the virtual account
 */
class VirtualAccountConfig
{
    public bool $reusableStatus;

    /**
     * VirtualAccountConfig constructor
     * @param bool $reusableStatus The reusable status of the virtual account
     */
    public function __construct(bool $reusableStatus)
    {
        $this->reusableStatus = $reusableStatus;
    }
}