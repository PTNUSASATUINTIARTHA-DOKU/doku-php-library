<?php
namespace Doku\Snap\Models\CheckStatus;

use Doku\Snap\Models\TotalAmount\TotalAmount;

class DirectDebitCheckStatusRequestDto
{
    public ?string $originalPartnerReferenceNo;
    public ?string $originalReferenceNo;
    public ?string $originalExternalId;
    public string $serviceCode;  // Mandatory
    public ?string $transactionDate;
    public ?TotalAmount $amount;  // Optional
    public ?string $merchantId;
    public ?string $subMerchantId;
    public ?string $externalStoreId;
    public ?CheckStatusAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        ?string $originalPartnerReferenceNo,
        ?string $originalReferenceNo,
        ?string $originalExternalId,
        string $serviceCode,
        ?string $transactionDate,
        ?TotalAmount $amount,
        ?string $merchantId,
        ?string $subMerchantId,
        ?string $externalStoreId,
        ?CheckStatusAdditionalInfoRequestDto $additionalInfo
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

    /**
     * Validasi dan kembalikan error pertama yang ditemukan.
     * @return array|null Response error jika validasi gagal, null jika valid.
     */
    public function validateCodeRequestDto(): ?array
    {
        // Validasi serviceCode (mandatory)
        if (empty($this->serviceCode)) {
            return [
                'responseCode' => '4005501',
                'responseMessage' => 'Service Code is required'
            ];
        } elseif ($this->serviceCode !== '55') {
            return [
                'responseCode' => '4005501',
                 'responseMessage' => 'Service Code must be "55"'
            ];
        }

       
        // Validasi transactionDate (optional, but must follow ISO 8601 if provided)
        if (!empty($this->transactionDate) && !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:Z|(\+|\-)\d{2}:\d{2})?$/', $this->transactionDate)) {
            return [
                'responseCode' => '4005501',
                'responseMessage' => 'Transaction Date must be in ISO 8601 format if provided'
            ];
        }

        // Jika semua validasi lolos, kembalikan null
        return null;
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'originalPartnerReferenceNo' => $this->originalPartnerReferenceNo,
            'originalReferenceNo' => $this->originalReferenceNo,
            'originalExternalId' => $this->originalExternalId,
            'serviceCode' => $this->serviceCode,
            'transactionDate' => $this->transactionDate,
            'amount' => $this->amount ? $this->amount->generateJSONBody() : null,
            'merchantId' => $this->merchantId,
            'subMerchantId' => $this->subMerchantId,
            'externalStoreId' => $this->externalStoreId,
            'additionalInfo' => $this->additionalInfo ? $this->additionalInfo->generateJSONBody() : null
        ]);
    }
}
