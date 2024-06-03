<?php

/**
 * Class PaymentNotificationResponseBodyDto
 * This class represents the body for payment notification response.
 */
class PaymentNotificationResponseBodyDto
{
    public string $responseCode; // e.g., 2002500
    public string $responseMessage;
    public NotificationVirtualAccountData $virtualAccountData;

    /**
     * Constructor for PaymentNotificationResponseBodyDto
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