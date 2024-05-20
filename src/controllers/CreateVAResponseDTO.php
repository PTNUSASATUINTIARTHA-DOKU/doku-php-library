<?php
/**
 * Class CreateVaResponseDTO
 * Represents the response data transfer object for creating a virtual account
 */
class CreateVaResponseDTO
{
    public string $responseCode;
    public string $responseMessage;
    public VirtualAccountData $virtualAccountData;
}