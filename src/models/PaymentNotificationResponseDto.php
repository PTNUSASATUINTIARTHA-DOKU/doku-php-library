<?php

/**
 * Class PaymentNotificationResponseDto
 * This class represents the payment notification response.
 */
class PaymentNotificationResponseDto
{
    public PaymentNotificationResponseHeaderDto $header;
    public PaymentNotificationResponseBodyDto $body;

    /**
     * Constructor for PaymentNotificationResponseDto
     *
     * @param PaymentNotificationResponseHeaderDto $header
     * @param PaymentNotificationResponseBodyDto $body
     */
    public function __construct(
        PaymentNotificationResponseHeaderDto $header,
        PaymentNotificationResponseBodyDto $body
    ) {
        $this->header = $header;
        $this->body = $body;
    }
}