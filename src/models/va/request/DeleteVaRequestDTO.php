<?php
namespace Doku\Snap\Models\VA\Request;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaRequestAdditionalInfo;
class DeleteVaRequestDTO
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $trxId;
    public DeleteVaRequestAdditionalInfo $additionalInfo;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $trxId,
        DeleteVaRequestAdditionalInfo $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->trxId = $trxId;
        $this->additionalInfo = $additionalInfo;
    }

    public function generateJSONBody(): string
    {
        $payload = array(
            'partnerServiceId' => $this->partnerServiceId,
            'customerNo' => $this->customerNo,
            'virtualAccountNo' => $this->virtualAccountNo,
            'trxId' => $this->trxId,
            'additionalInfo' => array(
                'channel'=> $this->additionalInfo->channel
            )
        );
        return json_encode($payload);
    }

    public function validateDeleteVaRequest(): bool
    {
        $status = true;
        $status &= $this->validatePartnerServiceId();
        $status &= $this->validateCustomerNo();
        $status &= $this->validateVirtualAccountNo();
        $status &= $this->validateTrxId();
        $status &= $this->validateAdditionalInfo();

        return true;
    }

    private function validatePartnerServiceId(): bool
    {
        return !is_null($this->partnerServiceId)
            && is_string($this->partnerServiceId)
            && strlen($this->partnerServiceId) <= 20
            && preg_match('/^\d+$/', $this->partnerServiceId);
    }

    private function validateCustomerNo(): bool
    {
        return !is_null($this->customerNo)
            && is_string($this->customerNo)
            && strlen($this->customerNo) === 8
            && preg_match('/^\s{0,7}\d{1,8}$/', $this->customerNo);
    }

    private function validateVirtualAccountNo(): bool
    {
        return !is_null($this->virtualAccountNo)
            && is_string($this->virtualAccountNo)
            && $this->virtualAccountNo === $this->partnerServiceId . $this->customerNo;
    }

    private function validateTrxId(): bool
    {
        return !is_null($this->trxId) && is_string($this->trxId);
    }

    private function validateAdditionalInfo(): bool
    {
        return $this->additionalInfo instanceof DeleteVaRequestAdditionalInfo;
    }
}