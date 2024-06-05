<?php

class VaController
{
    private VaServices $vaServices;
    private TokenServices $tokenServices;
    public function __construct() {
        $this->vaServices = new VaServices();
        $this->tokenServices = new TokenServices();
    }

    /**
     * Creates a virtual account using the provided CreateVaRequestDTO and other parameters.
     *
     * @param CreateVaRequestDTO $createVaRequestDTO The DTO containing the request data.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     * @param string $tokenB2B The B2B token.
     * @param bool $isProduction Whether to use the production or sandbox environment.
     * @return CreateVaResponseDTO The DTO containing the response data.
     */
    public function createVa(CreateVaRequestDTO $createVaRequestDTO, string $privateKey, string $clientId, string $tokenB2B, bool $isProduction): CreateVaResponseDTO
    {
        $externalId = $this->vaServices->generateExternalId();
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);
        $requestHeaderDTO = $this->vaServices->createVaRequestHeaderDTO($createVaRequestDTO, $privateKey, $clientId, $tokenB2B, $timestamp, $externalId, $signature);
        $createVaResponseDTO = $this->vaServices->createVa($requestHeaderDTO, $createVaRequestDTO, $isProduction);
        return $createVaResponseDTO;
    }
}