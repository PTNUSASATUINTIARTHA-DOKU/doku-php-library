<?php
/**
 * Class VirtualAccountConfig
 * Represents the configuration for the virtual account
 */
class CreateVaVirtualAccountConfig extends VirtualAccountConfig
{
    public ?bool $reusableStatus;
    public ?string $minAmount;
    public ?string $maxAmount;

    /**
     * VirtualAccountConfig constructor
     * @param bool $reusableStatus The reusable status of the virtual account
     */
    public function __construct(?bool $reusableStatus, ?string $minAmount = null, ?string $maxAmount = null)
    {
        $this->reusableStatus = $reusableStatus;
        $this->minAmount = $minAmount;
        $this->maxAmount = $maxAmount;
    }
}