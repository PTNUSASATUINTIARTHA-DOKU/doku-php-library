<?php
namespace Doku\Snap\Controllers;
use Doku\Snap\Services\VaServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\VA\Request\CreateVaRequestDTO;
use Doku\Snap\Models\VA\Response\CreateVaResponseDTO;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDTO;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDTO;
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDTO;
use Doku\Snap\Models\VA\Response\CheckStatusVAResponseDTO;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDTO;
use Doku\Snap\Models\VA\Response\DeleteVaResponseDTO;
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
        $requestHeaderDTO = $this->vaServices->generateRequestHeaderDTO($timestamp, $signature, $clientId, $externalId, $createVaRequestDTO->additionalInfo->channel, $tokenB2B);
        $createVaResponseDTO = $this->vaServices->createVa($requestHeaderDTO, $createVaRequestDTO, $isProduction);
        return $createVaResponseDTO;
    }

    /**
     * Updates a virtual account using the provided UpdateVaRequestDTO and other parameters.
     *
     * @param UpdateVaRequestDTO $UpdateVaRequestDTO The DTO containing the request data.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     * @param string $tokenB2B The B2B token.
     * @param string $secretKey The secret key for signing the request.
     * @param bool $isProduction Whether to use the production or sandbox environment.
     * @return UpdateVaResponseDTO The DTO containing the response data.
     */
    public function doUpdateVa(
        UpdateVaRequestDTO $UpdateVaRequestDTO,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        string $isProduction
    ): UpdateVaResponseDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . UPDATE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $UpdateVaRequestDTO->getJSONRequestBody(),
            $timestamp,
            $secretKey
        );
        $externalId = $this->vaServices->generateExternalId();
        $header = $this->vaServices->generateRequestHeaderDTO(
            $UpdateVaRequestDTO->additionalInfo->channel,
            $clientId,
            $tokenB2B,
            $timestamp,
            $signature,
            $externalId
        );

        return $this->vaServices->doUpdateVa($header, $UpdateVaRequestDTO);
    }

    /**
     * Deletes a payment code using the provided DeleteVaRequestDto and other parameters.
     *
     * @param DeleteVaRequestDto $deleteVaRequestDto The DTO containing the request data.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     * @param string $secretKey The secret key for signing the request.
     * @param string $tokenB2B The B2B token.
     * @param string $isProduction Whether to use the production or sandbox environment.
     * @return DeleteVaResponseDTO The DTO containing the response data.
     */
    public function doDeletePaymentCode(
        DeleteVaRequestDto $deleteVaRequestDto, 
        string $privateKey, 
        string $clientId, 
        string $secretKey, 
        string $tokenB2B, 
        string $isProduction
    ): DeleteVaResponseDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = getBaseURL(false);

        $apiEndpoint = $baseUrl . DELETE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            "DELETE",
            $apiEndpoint,
            $tokenB2B,
            $deleteVaRequestDto->getJSONRequestBody(),
            $timestamp,
            $secretKey
        );

        $externalId = $this->vaServices->generateExternalId();
        $requestHeaderDto = $this->vaServices->generateRequestHeaderDTO(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            $deleteVaRequestDto->additionalInfo->channel, 
            $tokenB2B, 
        );

        $response = $this->vaServices->doDeletePaymentCode($requestHeaderDto, $deleteVaRequestDto);
        
        return $response;
    }

    /**
     * Checks the status of a virtual account using the provided CheckStatusVaRequestDTO and other parameters.
     *
     * @param CheckStatusVaRequestDTO $checkVARequestDTO The DTO containing the request data.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     * @param string $tokenB2B The B2B token.
     * @param bool $isProduction Whether to use the production or sandbox environment.
     * @return CheckStatusVAResponseDTO The DTO containing the response data.
     */
    public function doCheckStatusVa(
        CheckStatusVaRequestDTO $checkVARequestDTO, 
        string $privateKey, 
        string $clientId, 
        string $tokenB2B, 
        bool $isProduction
    ): CheckStatusVAResponseDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . UPDATE_VA_URL;

        $signature = $this->tokenServices->generateSymmetricSignature(
            "POST",
            $apiEndpoint,
            $tokenB2B,
            $checkVARequestDTO->getJSONRequestBody(),
            $timestamp,
            $privateKey
        );

        $externalId = $this->vaServices->generateExternalId();

        $header = $this->vaServices->generateRequestHeaderDTO(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            "SDK", // Use 'SDK' as the channel ID
            $tokenB2B, 
        );

        return $this->vaServices->doCheckStatusVa($header, $checkVARequestDTO);
    }
}