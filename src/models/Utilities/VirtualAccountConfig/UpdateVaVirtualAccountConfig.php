<?php
namespace Doku\Snap\Models\Utilities\VirtualAccountConfig;
class UpdateVaVirtualAccountConfig
{
    public ?string $status;
    public ?string $minAmount;
    public ?string $maxAmount;

    public function __construct(?string $status, ?string $minAmount=null, ?string $maxAmount=null)
    {
        $this->status = $status;
        $this->minAmount = $minAmount;
        $this->maxAmount = $maxAmount;
    }
}