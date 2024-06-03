<?php

/**
 * Class PaymentNotificationResponseHeaderDto
 * This class represents the header for payment notification response.
 */
class PaymentNotificationResponseHeaderDto
{
    public string $xTimestamp;
    public string $contentType = "application/json";

    /**
     * Constructor for PaymentNotificationResponseHeaderDto
     *
     * @param string $xTimestamp
     */
    public function __construct(string $xTimestamp)
    {
        $this->xTimestamp = $xTimestamp;
    }
}