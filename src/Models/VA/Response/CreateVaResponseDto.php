<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\VA\VirtualAccountData\CreateVaResponseVirtualAccountData;
class CreateVaResponseDto
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