<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\Utilities\VirtualAccountData\UpdateVaResponseVirtualAccountData;
class UpdateVaResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?UpdateVaResponseVirtualAccountData $virtualAccountData;
    public function __construct(
        ?string $responseCode, 
        ?string $responseMessage, 
        ?UpdateVaResponseVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}