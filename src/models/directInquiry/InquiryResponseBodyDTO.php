<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryResponseBodyDto
{
    public string $responseCode;
    public string $responseMessage;
    public InquiryRequestVirtualAccountDataDto $virtualAccountData;

    public function __construct(
        string $responseCode,
        string $responseMessage,
        InquiryRequestVirtualAccountDataDto $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}