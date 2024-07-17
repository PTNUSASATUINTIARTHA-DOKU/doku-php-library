<?php

namespace Doku\Snap\Models;
class PaymentNotificationResponseHeaderDTO
{
    public string $xTimestamp;
    public string $contentType = "application/json";

    /**
     * Constructor for PaymentNotificationResponseHeaderDTO
     *
     * @param string $xTimestamp
     */
    public function __construct(string $xTimestamp)
    {
        $this->xTimestamp = $xTimestamp;
    }
}