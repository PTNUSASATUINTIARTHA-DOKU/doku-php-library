<?php

class CheckStatusVaResponseDTO
{
    public string $responseCode;
    public string $responseMessage;
    public CheckStatusVirtualAccountData $virtualAccountData;

    public function __construct(
        string $responseCode,
        string $responseMessage,
        CheckStatusVirtualAccountData $virtualAccountData
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->virtualAccountData = $virtualAccountData;
    }
}