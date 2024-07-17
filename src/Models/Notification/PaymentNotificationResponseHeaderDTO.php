<?php

namespace Doku\Snap\Models\Notification;
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

    public function generateJSONHeader(): string
    {
        $payload = array(
            'xTimestamp' => $this->xTimestamp
        );
        return json_encode($payload);
    }
}