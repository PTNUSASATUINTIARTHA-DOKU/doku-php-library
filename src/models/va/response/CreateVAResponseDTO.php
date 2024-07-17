<?php
namespace Doku\Snap\Models;
class CreateVaResponseDTO
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?CreateVaResponseVirtualAccountData $virtualAccountData;


    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?CreateVaResponseVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}