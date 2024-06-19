<?php
require "src/services/VaServices.php";
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
        $requestHeaderDTO = $this->vaServices->createVaRequestHeaderDTO($timestamp, $signature, $clientId, $externalId, $createVaRequestDTO->additionalInfo->channel, $tokenB2B);
        $createVaResponseDTO = $this->vaServices->createVa($requestHeaderDTO, $createVaRequestDTO, $isProduction);
        return $createVaResponseDTO;
    }

    /**
     * Updates a virtual account using the provided UpdateVaDTO and other parameters.
     *
     * @param UpdateVaDTO $UpdateVaDTO The DTO containing the request data.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     * @param string $tokenB2B The B2B token.
     * @param string $secretKey The secret key for signing the request.
     * @param bool $isProduction Whether to use the production or sandbox environment.
     * @return UpdateVaResponseDTO The DTO containing the response data.
     */
    public function doUpdateVa(
        UpdateVaDTO $UpdateVaDTO,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        string $isProduction
    ): UpdateVaResponseDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . ACCESS_TOKEN;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $UpdateVaDTO,
            $timestamp
        );
        $externalId = $this->vaServices->generateExternalId();
        $header = $this->vaServices->createVaRequestHeaderDTO(
            $UpdateVaDTO->additionalInfo->channel,
            $clientId,
            $tokenB2B,
            $timestamp,
            $signature,
            $externalId
        );

        return $this->vaServices->doUpdateVa($header, $UpdateVaDTO);
    }
}