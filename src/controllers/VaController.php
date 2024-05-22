<?php

class VaController
{
    private $privateKey;
    private $clientId;
    private $tokenB2B;

    public function __construct(string $privateKey, string $clientId, string $tokenB2B)
    {
        $this->privateKey = $privateKey;
        $this->clientId = $clientId;
        $this->tokenB2B = $tokenB2B;
    }

    public function createVa(CreateVaRequestDto $createVaRequestDto)
    {
        // Create an instance of the service class responsible for creating a virtual account
        $vaServices = new VaServices($this->privateKey, $this->clientId, $this->tokenB2B);
        $createVaResponseDto = $vaServices->createVa($createVaRequestDto);

        // Return the response from the service
        return $createVaResponseDto;
    }
}