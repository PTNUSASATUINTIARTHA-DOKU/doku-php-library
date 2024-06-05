<?php

require_once "src/models/PaymentNotificationResponseHeaderDTO.php";
require_once "src/models/PaymentNotificationResponseBodyDTO.php";

/**
 * Class PaymentNotificationResponseDTO
 * This class represents the payment notification response.
 */
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
}