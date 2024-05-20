<?php
/**
 * Class VirtualAccountData
 * Represents the virtual account data
 */
class VirtualAccountData
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $virtualAccountName;
    public string $virtualAccountEmail;
    public string $trxId;
    public TotalAmount $totalAmount;
    public AdditionalInfo $additionalInfo;
}