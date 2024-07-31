<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryRequestBodyDto
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $channelCode;
    public string $trxDateInit;
    public string $language;
    public string $inquiryRequestId;
    public InquiryRequestAdditionalInfoDto $additionalInfo;

    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $channelCode,
        string $trxDateInit,
        string $language,
        string $inquiryRequestId,
        InquiryRequestAdditionalInfoDto $additionalInfo
    ) {
        $this->partnerServiceId = $partnerServiceId;
        $this->customerNo = $customerNo;
        $this->virtualAccountNo = $virtualAccountNo;
        $this->channelCode = $channelCode;
        $this->trxDateInit = $trxDateInit;
        $this->language = $language;
        $this->inquiryRequestId = $inquiryRequestId;
        $this->additionalInfo = $additionalInfo;
    }
}