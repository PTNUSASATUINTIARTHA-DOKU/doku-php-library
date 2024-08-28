<?php
namespace Doku\Snap\Models\PaymentJumpApp;
use Doku\Snap\Models\TotalAmount\TotalAmount;

class PaymentJumpAppRequestDto
{
    public ?string $partnerReferenceNo;
    public ?string $validUpTo;
    public ?string $pointOfInitiation;
    public ?UrlParamDto $urlParam;
    public ?TotalAmount $amount;
    public ?PaymentJumpAppAdditionalInfoRequestDto $additionalInfo;

    public function __construct(
        ?string $partnerReferenceNo,
        ?string $validUpTo,
        ?string $pointOfInitiation,
        ?UrlParamDto $urlParam,
        ?TotalAmount $amount,
        ?PaymentJumpAppAdditionalInfoRequestDto $additionalInfo
    ) {
        $this->partnerReferenceNo = $partnerReferenceNo;
        $this->validUpTo = $validUpTo;
        $this->pointOfInitiation = $pointOfInitiation;
        $this->urlParam = $urlParam;
        $this->amount = $amount;
        $this->additionalInfo = $additionalInfo;
    }

    public function validatePaymentJumpAppRequestDto(): void
    {
        if (empty($this->partnerReferenceNo)) {
            throw new \InvalidArgumentException('Partner Reference Number is required');
        }
        if (empty($this->merchantId)) {
            throw new \InvalidArgumentException('Merchant ID is required');
        }
        if (empty($this->merchantTradeNo)) {
            throw new \InvalidArgumentException('Merchant Trade No is required');
        }
        if (empty($this->amount) || !is_numeric($this->amount)) {
            throw new \InvalidArgumentException('Valid amount is required');
        }
        if (empty($this->currency)) {
            throw new \InvalidArgumentException('Currency is required');
        }
        if (!in_array($this->additionalInfo->channel, ['EMONEY_SHOPEE_PAY_SNAP', 'EMONEY_DANA_SNAP'])) {
            throw new \InvalidArgumentException('Invalid channel');
        }
        if (empty($this->deviceId)) {
            throw new \InvalidArgumentException('Device ID is required');
        }
    }

    public function generateJSONBody(): string
    {
        $amountArr = array(
            'value' => $this->amount->value,
            'currency' => $this->amount->currency
        );

        $urlParamArr = array(
            'url' => $this->urlParam->url,
            'type' => $this->urlParam->type,
            'isDeepLink' => $this->urlParam->isDeepLink
        );

        $additionalInfoArr = array(
            'channel' => $this->additionalInfo->channel
        );

        if ($this->additionalInfo->channel === 'EMONEY_DANA_SNAP') {
            $additionalInfoArr['orderTitle'] = $this->additionalInfo->orderTitle;
        } elseif ($this->additionalInfo->channel === 'EMONEY_SHOPEE_PAY_SNAP') {
            $additionalInfoArr['metadata'] = $this->additionalInfo->metadata;
        }

        $payload = array(
            'partnerReferenceNo' => $this->partnerReferenceNo,
            'validUpTo' => $this->validUpTo,
            'pointOfInitiation' => $this->pointOfInitiation,
            'urlParam' => $urlParamArr,
            'amount' => $amountArr,
            'additionalInfo' => $additionalInfoArr
        );

        return json_encode($payload);
    }
}