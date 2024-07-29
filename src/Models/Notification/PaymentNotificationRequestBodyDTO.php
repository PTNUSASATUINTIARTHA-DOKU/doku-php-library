<?php
namespace Doku\Snap\Models\Notification;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
class PaymentNotificationRequestBodyDto
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $trxId;
    public string $paymentRequestId;
    public TotalAmount $paidAmount;
    public string $virtualAccountEmail;
    public string $virtualAccountPhone;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $virtualAccountName,
        string $trxId,
        string $paymentRequestId,
        TotalAmount $paidAmount,
        string $virtualAccountEmail,
        string $virtualAccountPhone
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->virtualAccountName = $virtualAccountName;
        $this->trxId = $trxId;
        $this->paymentRequestId = $paymentRequestId;
        $this->paidAmount = $paidAmount;
        $this->virtualAccountEmail = $virtualAccountEmail;
        $this->virtualAccountPhone = $virtualAccountPhone;
    }
}