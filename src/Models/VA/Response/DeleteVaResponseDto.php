<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\VA\VirtualAccountData\DeleteVaResponseVirtualAccountData;
class DeleteVaResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?DeleteVaResponseVirtualAccountData $virtualAccountData;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?DeleteVaResponseVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}

