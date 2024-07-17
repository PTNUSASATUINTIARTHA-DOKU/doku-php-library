<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\Utilities\VirtualAccountData\CreateVaResponseVirtualAccountData;
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