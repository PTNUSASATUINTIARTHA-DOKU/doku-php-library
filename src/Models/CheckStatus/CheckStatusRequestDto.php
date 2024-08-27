<?php

namespace Doku\Snap\Models\CheckStatus;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\AdditionalInfo\CheckStatusAdditionalInfoRequestDto;

class CheckStatusRequestDto
{
    public string $originalPartnerReferenceNo;
    public string $originalReferenceNo;
    public string $originalExternalId;
    public string $serviceCode;
    public string $transactionDate;
    public TotalAmount $amount;
    public string $merchantId;
    public string $subMerchantId;
    public string $externalStoreId;
    public CheckStatusAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        string $originalPartnerReferenceNo,
        string $originalReferenceNo,
        string $originalExternalId,
        string $serviceCode,
        string $transactionDate,
        TotalAmount $amount,
        string $merchantId,
        string $subMerchantId,
        string $externalStoreId,
        CheckStatusAdditionalInfoRequestDto $additionalInfo
    ) {
        $this->originalPartnerReferenceNo = $originalPartnerReferenceNo;
        $this->originalReferenceNo = $originalReferenceNo;
        $this->originalExternalId = $originalExternalId;
        $this->serviceCode = $serviceCode;
        $this->transactionDate = $transactionDate;
        $this->amount = $amount;
        $this->merchantId = $merchantId;
        $this->subMerchantId = $subMerchantId;
        $this->externalStoreId = $externalStoreId;
        $this->additionalInfo = $additionalInfo;
    }

    public function validateCheckStatusRequestDto(): void
    {
        if (empty($this->originalPartnerReferenceNo)) {
            throw new \InvalidArgumentException("Original Partner Reference Number is required");
        }
        if (empty($this->originalReferenceNo)) {
            throw new \InvalidArgumentException("Original Reference Number is required");
        }
        if (empty($this->originalExternalId)) {
            throw new \InvalidArgumentException("Original External ID is required");
        }
        if (empty($this->serviceCode)) {
            throw new \InvalidArgumentException("Service Code is required");
        }
        if (empty($this->transactionDate)) {
            throw new \InvalidArgumentException("Transaction Date is required");
        }
        if (empty($this->merchantId)) {
            throw new \InvalidArgumentException("Merchant ID is required");
        }
        if (empty($this->subMerchantId)) {
            throw new \InvalidArgumentException("Sub Merchant ID is required");
        }
        if (empty($this->externalStoreId)) {
            throw new \InvalidArgumentException("External Store ID is required");
        }

        // Validate TotalAmountDto
        if (empty($this->amount->value) || !is_numeric($this->amount->value)) {
            throw new \InvalidArgumentException("Amount value is required and must be numeric");
        }
        if (empty($this->amount->currency)) {
            throw new \InvalidArgumentException("Amount currency is required");
        }

        // Validate CheckStatusAdditionalInfoRequestDto
        if (empty($this->additionalInfo->deviceId)) {
            throw new \InvalidArgumentException("Device ID is required");
        }
        if (empty($this->additionalInfo->channel)) {
            throw new \InvalidArgumentException("Channel is required");
        }
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'originalPartnerReferenceNo' => $this->originalPartnerReferenceNo,
            'originalReferenceNo' => $this->originalReferenceNo,
            'originalExternalId' => $this->originalExternalId,
            'serviceCode' => $this->serviceCode,
            'transactionDate' => $this->transactionDate,
            'amount' => $this->amount->generateJSONBody(),
            'merchantId' => $this->merchantId,
            'subMerchantId' => $this->subMerchantId,
            'externalStoreId' => $this->externalStoreId,
            'additionalInfo' => $this->additionalInfo->generateJSONBody()
        ]);
    }
}