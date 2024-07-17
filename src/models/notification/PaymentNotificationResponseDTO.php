<?php

namespace Doku\Snap\Models\Notification;
class PaymentNotificationResponseDTO
{
    public PaymentNotificationResponseHeaderDTO $header;
    public PaymentNotificationResponseBodyDTO $body;

    /**
     * Constructor for PaymentNotificationResponseDTO
     *
     * @param PaymentNotificationResponseHeaderDTO $header
     * @param PaymentNotificationResponseBodyDTO $body
     */
    public function __construct(
        PaymentNotificationResponseHeaderDTO $header,
        PaymentNotificationResponseBodyDTO $body
    ) {
        $this->header = $header;
        $this->body = $body;
    }
    public function generateJSONHeader(): string
    {
        return $this->header->generateJSONHeader();
    }

    public function generateJSONBody(): string
    {
        return $this->body->generateJSONBody();
    }
}