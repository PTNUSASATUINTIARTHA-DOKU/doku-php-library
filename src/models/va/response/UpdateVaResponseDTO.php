<?php
namespace Doku\Snap\Models\VA\Response;
use Doku\Snap\Models\Utilities\VirtualAccountData\UpdateVaResponseVirtualAccountData;
class UpdateVaResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?UpdateVaResponseVirtualAccountData $virtualAccountData;

    /**
     * UpdateVaResponseDto constructor.
     * @param string $responseCode The response code.
     * @param string $responseMessage The response message.
     * @param UpdateVaResponseVirtualAccountData $virtualAccountData The virtual account data.
     */
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