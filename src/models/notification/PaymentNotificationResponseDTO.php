<?php

namespace Doku\Snap\Models\Notification;
class PaymentNotificationResponseDto
{
    public PaymentNotificationResponseHeaderDto $header;
    public PaymentNotificationResponseBodyDto $body;

    public function __construct(
        PaymentNotificationResponseHeaderDto $header,
        PaymentNotificationResponseBodyDto $body
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