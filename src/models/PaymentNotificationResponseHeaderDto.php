<?php

/**
 * Class PaymentNotificationResponseHeaderDTO
 * This class represents the header for payment notification response.
 */
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