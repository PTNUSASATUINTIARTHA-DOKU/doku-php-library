<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\VA\VirtualAccountData\CheckStatusVirtualAccountData;
use Doku\Snap\Models\VA\AdditionalInfo\CheckStatusVaResponseAdditionalInfo;
class CheckStatusVaResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?CheckStatusVirtualAccountData $virtualAccountData;
    public ?CheckStatusVaResponseAdditionalInfo $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?CheckStatusVirtualAccountData $virtualAccountData,
        ?CheckStatusVaResponseAdditionalInfo $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
        $this->additionalInfo = $additionalInfo;
    }
}