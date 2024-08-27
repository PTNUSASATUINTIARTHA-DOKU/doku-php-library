<?php
namespace Doku\Snap\Models\VA\VirtualAccountConfig;
class CreateVaVirtualAccountConfig
{
    public ?bool $reusableStatus;
    public ?string $minAmount;
    public ?string $maxAmount;
    public function __construct(?bool $reusableStatus, ?string $minAmount = null, ?string $maxAmount = null)
    {
        $this->reusableStatus = $reusableStatus;
        $this->minAmount = $minAmount;
        $this->maxAmount = $maxAmount;
    }
}