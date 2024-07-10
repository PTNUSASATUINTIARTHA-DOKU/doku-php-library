<?php
/**
 * Class CreateVaResponseDTO
 * Represents the response data transfer object for creating a virtual account
 */
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