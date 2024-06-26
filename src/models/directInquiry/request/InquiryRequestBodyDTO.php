<?php

class InquiryRequestBodyDTO
{
    public string $partnerServiceId;
    public string $customerNo;
    public string $virtualAccountNo;
    public string $channelCode;
    public string $trxDateInit;
    public string $language;
    public string $inquiryRequestId;
    public InquiryRequestAdditionalInfoDTO $additionalInfo;

    /**
     * InquiryRequestBodyDTO constructor.
     *
     * @param string $partnerServiceId
     * @param string $customerNo
     * @param string $virtualAccountNo
     * @param string $channelCode
     * @param string $trxDateInit
     * @param string $language
     * @param string $inquiryRequestId
     * @param InquiryRequestAdditionalInfoDTO $additionalInfo
     */
    public function __construct(
        string $partnerServiceId,
        string $customerNo,
        string $virtualAccountNo,
        string $channelCode,
        string $trxDateInit,
        string $language,
        string $inquiryRequestId,
        InquiryRequestAdditionalInfoDTO $additionalInfo
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