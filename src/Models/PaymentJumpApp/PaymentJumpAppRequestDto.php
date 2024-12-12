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

    public function validatePaymentJumpAppRequestDto()
    {
        if (empty($this->partnerReferenceNo)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'partnerReferenceNo is required'
            ];
        }
        if (empty($this->urlParam)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'urlParam is required'
            ];
        }
        if (empty($this->amount)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount is required'
            ];
        }
        if (empty($this->amount->value)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount.value is required'
            ];
        }
        if (empty($this->amount->currency)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'amount.currency is required'
            ];
        }
        if (!in_array($this->additionalInfo->channel, ['EMONEY_SHOPEE_PAY_SNAP', 'EMONEY_DANA_SNAP'])) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'Invalid channel'
            ];
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
            'channel' => $this->additionalInfo->channel,
            'origin' => $this->additionalInfo->origin->toArray()
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