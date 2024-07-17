<?php

namespace Doku\Snap\Models\Notification;
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

    public function generateJSONBody(): string
    {
        $payload = array(
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'partnerServiceId' => $this->virtualAccountData->partnerServiceId,
            'customerNo' => $this->virtualAccountData->customerNo,
            'virtualAccountNo' => $this->virtualAccountData->virtualAccountNo,
            'virtualAccountName' => $this->virtualAccountData->virtualAccountName,
            'paymentRequestId' => $this->virtualAccountData->paymentRequestId
        );
        return json_encode($payload);
    }
}