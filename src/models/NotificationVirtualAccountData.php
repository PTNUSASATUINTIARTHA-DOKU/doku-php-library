<?php

/**
 * Class NotificationVirtualAccountData
 * This class represents the virtual account data for payment notification.
 */
class NotificationVirtualAccountData
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $paymentRequestId;

    /**
     * Constructor for NotificationVirtualAccountData
     *
     * @param string $partnerServiceId
     * @param string $customerNo
     * @param string $virtualAccountNo
     * @param string $virtualAccountName
     * @param string $paymentRequestId
     */
    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $virtualAccountName,
        string $paymentRequestId
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->paymentRequestId = $paymentRequestId;
    }
}