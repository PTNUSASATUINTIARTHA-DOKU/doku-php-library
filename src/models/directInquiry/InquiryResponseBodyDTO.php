<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryResponseBodyDto
{
    public string $responseCode;
    public string $responseMessage;
    public InquiryResponseVirtualAccountDataDto $virtualAccountData;

    public function __construct(
        string $responseCode,
        string $responseMessage,
        InquiryResponseVirtualAccountDataDto $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}