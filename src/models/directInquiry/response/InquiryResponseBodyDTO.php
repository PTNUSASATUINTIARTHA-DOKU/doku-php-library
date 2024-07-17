<?php
namespace Doku\Snap\Models;
class InquiryResponseBodyDTO
{
    public string $responseCode;
    public string $responseMessage;
    public InquiryRequestVirtualAccountDataDTO $virtualAccountData;

    /**
     * InquiryResponseBodyDTO constructor.
     *
     * @param string $responseCode
     * @param string $responseMessage
     * @param InquiryRequestVirtualAccountDataDTO $virtualAccountData
     */
    public function __construct(
        string $responseCode,
        string $responseMessage,
        InquiryRequestVirtualAccountDataDTO $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}