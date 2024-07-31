<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\Utilities\VirtualAccountData\CheckStatusVirtualAccountData;
class CheckStatusVaResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?CheckStatusVirtualAccountData $virtualAccountData;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?CheckStatusVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}