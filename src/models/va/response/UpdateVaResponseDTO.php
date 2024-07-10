<?php
/**
 * Class UpdateVaResponseDto
 * Represents the response data for updating a virtual account.
 */
class UpdateVaResponseDTO
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?UpdateVaRequestDTO $virtualAccountData;

    /**
     * UpdateVaResponseDto constructor.
     * @param string $responseCode The response code.
     * @param string $responseMessage The response message.
     * @param UpdateVaRequestDTO $virtualAccountData The virtual account data.
     */
    public function __construct(
        ?string $responseCode, 
        ?string $responseMessage, 
        ?UpdateVaRequestDTO $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}