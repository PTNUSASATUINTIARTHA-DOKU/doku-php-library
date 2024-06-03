<?php

/**
 * Class PaymentNotificationResponseBodyDTO
 * This class represents the body for payment notification response.
 */
class PaymentNotificationResponseBodyDTO
{
    public string $responseCode; // e.g., 2002500
    public string $responseMessage;
    public NotificationVirtualAccountData $virtualAccountData;

    /**
     * Constructor for PaymentNotificationResponseBodyDTO
     *
     * @param string $responseCode
     * @param string $responseMessage
     * @param NotificationVirtualAccountData $virtualAccountData
     */
    public function __construct(
        string $responseCode,
        string $responseMessage,
        NotificationVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}