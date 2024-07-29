<?php
namespace Doku\Snap\Models\Utilities\VirtualAccountConfig;
class UpdateVaVirtualAccountConfig
{
    public string $status;
    public int $minAmount;
    public int $maxAmount;
    /**
     * UpdateVaVirtualAccountConfig constructor.
     * @param string $status The status of the virtual account.
     */
    public function __construct(?string $status)
    {
        $this->status = $status;
    }
}