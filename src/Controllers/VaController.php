<?php
namespace Doku\Snap\Controllers;
use Doku\Snap\Services\VaServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\VA\Response\CreateVaResponseDto;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDto;
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Response\CheckStatusVAResponseDto;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\Response\DeleteVaResponseDto;
use Doku\Snap\Commons\Config;
class VaController
{
    private VaServices $vaServices;
    private TokenServices $tokenServices;
    public function __construct() {
        $this->vaServices = new VaServices();
        $this->tokenServices = new TokenServices();
    }

    public function createVa(CreateVaRequestDto $createVaRequestDto, string $privateKey, string $clientId, string $tokenB2B, bool $isProduction): CreateVaResponseDto
    {
        $externalId = $this->vaServices->generateExternalId();
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);
        $requestHeaderDto = $this->vaServices->generateRequestHeaderDto($timestamp, $signature, $clientId, $externalId, $createVaRequestDto->additionalInfo->channel, $tokenB2B);
        $createVaResponseDto = $this->vaServices->createVa($requestHeaderDto, $createVaRequestDto, $isProduction);
        return $createVaResponseDto;
    }

    public function doUpdateVa(
        UpdateVaRequestDto $UpdateVaRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        string $isProduction
    ): UpdateVaResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::UPDATE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $UpdateVaRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $externalId = $this->vaServices->generateExternalId();
        $header = $this->vaServices->generateRequestHeaderDto(
            $UpdateVaRequestDto->additionalInfo->channel,
            $clientId,
            $tokenB2B,
            $timestamp,
            $signature,
            $externalId
        );

        return $this->vaServices->doUpdateVa($header, $UpdateVaRequestDto);
    }

    public function doDeletePaymentCode(
        DeleteVaRequestDto $deleteVaRequestDto, 
        string $privateKey, 
        string $clientId, 
        string $secretKey, 
        string $tokenB2B, 
        string $isProduction
    ): DeleteVaResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = Config::getBaseURL(false);

        $apiEndpoint = $baseUrl . Config::DELETE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            "DELETE",
            $apiEndpoint,
            $tokenB2B,
            $deleteVaRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );

        $externalId = $this->vaServices->generateExternalId();
        $requestHeaderDto = $this->vaServices->generateRequestHeaderDto(
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

    public function doCheckStatusVa(
        CheckStatusVaRequestDto $checkVARequestDto, 
        string $privateKey, 
        string $clientId, 
        string $tokenB2B, 
        bool $isProduction
    ): CheckStatusVAResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::UPDATE_VA_URL;

        $signature = $this->tokenServices->generateSymmetricSignature(
            "POST",
            $apiEndpoint,
            $tokenB2B,
            $checkVARequestDto->generateJSONBody(),
            $timestamp,
            $privateKey
        );

        $externalId = $this->vaServices->generateExternalId();

        $header = $this->vaServices->generateRequestHeaderDto(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            "SDK", // Use 'SDK' as the channel ID
            $tokenB2B, 
        );

        return $this->vaServices->doCheckStatusVa($header, $checkVARequestDto);
    }
}