<?php
namespace Doku\Snap\Models\Notification;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\AdditionalInfo\PaymentNotificationRequestAdditionalInfo;
class PaymentNotificationRequestBodyDto
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $virtualAccountEmail;
    public string $trxId;
    public string $paymentRequestId;
    public TotalAmount $paidAmount;
    public string $virtualAccountPhone;
    public PaymentNotificationRequestAdditionalInfo $additionalInfo;
    public string $trxDateTime;
    public string $virtualAccountTrxType;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $virtualAccountName,
        string $virtualAccountEmail,
        string $trxId,
        string $paymentRequestId,
        TotalAmount $paidAmount,
        string $virtualAccountPhone,
        PaymentNotificationRequestAdditionalInfo $additionalInfo,
        string $trxDateTime,
        string $virtualAccountTrxType
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
        $this->additionalInfo = $additionalInfo;
        $this->trxDateTime = $trxDateTime;
        $this->virtualAccountTrxType = $virtualAccountTrxType;
    }
}