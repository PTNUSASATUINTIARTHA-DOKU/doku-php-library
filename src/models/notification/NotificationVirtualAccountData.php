<?php

namespace Doku\Snap\Models\Notification;
class NotificationVirtualAccountData
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $paymentRequestId;

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