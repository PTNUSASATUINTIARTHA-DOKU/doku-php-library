<?php
namespace Doku\Snap\Models;
class PaymentNotificationRequestBodyDTO
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

    /**
     * Constructor for PaymentNotificationRequestBodyDTO
     *
     * @param string $partnerServiceId
     * @param string $customerNo
     * @param string $virtualAccountNo
     * @param string $virtualAccountName
     * @param string $trxId
     * @param string $paymentRequestId
     * @param TotalAmount $paidAmount
     * @param string $virtualAccountEmail
     * @param string $virtualAccountPhone
     */
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