<?php
namespace Doku\Snap\Models;
class UpdateVaVirtualAccountConfig
{
    public string $status;
    /**
     * UpdateVaVirtualAccountConfig constructor.
     * @param string $status The status of the virtual account.
     */
    public function __construct(?string $status)
    {
        $this->status = $status;
    }
}