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
use Doku\Snap\Commons\Helper;
class VaController
{
    private VaServices $vaServices;
    private TokenServices $tokenServices;
    public function __construct() {
        $this->vaServices = new VaServices();
        $this->tokenServices = new TokenServices();
    }

    public function createVa(CreateVaRequestDto $createVaRequestDto, string $privateKey, string $clientId, string $tokenB2B, string $secretKey, bool $isProduction): CreateVaResponseDto
    {
        $externalId = Helper::generateExternalId();;
        $timestamp = $this->tokenServices->getTimestamp();
        $apiEndpoint = Config::CREATE_VA;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $createVaRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $requestHeaderDto = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            $createVaRequestDto->additionalInfo->channel, 
            $tokenB2B, 
            null,
            null,
            null
        );
        $createVaResponseDto = $this->vaServices->createVa($requestHeaderDto, $createVaRequestDto, $isProduction);
        return $createVaResponseDto;
    }

    public function doUpdateVa(
        UpdateVaRequestDto $updateVaRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        string $isProduction
    ): UpdateVaResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $apiEndpoint = Config::UPDATE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $updateVaRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $externalId = Helper::generateExternalId();;
        $header = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            $updateVaRequestDto->additionalInfo->channel, 
            $tokenB2B, 
            null,
            null,
            null
        );

        return $this->vaServices->doUpdateVa($header, $updateVaRequestDto);
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

        $apiEndpoint =Config::DELETE_VA_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            "DELETE",
            $apiEndpoint,
            $tokenB2B,
            $deleteVaRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );

        $externalId = Helper::generateExternalId();;
        $requestHeaderDto = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            $deleteVaRequestDto->additionalInfo->channel, 
            $tokenB2B, 
            null,
            null,
            null
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
        $apiEndpoint = Config::CHECK_VA;

        $signature = $this->tokenServices->generateSymmetricSignature(
            "POST",
            $apiEndpoint,
            $tokenB2B,
            $checkVARequestDto->generateJSONBody(),
            $timestamp,
            $privateKey
        );

        $externalId = Helper::generateExternalId();;

        $header = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature,
            $clientId, 
            $externalId,
            "SDK", // Use 'SDK' as the channel ID
            $tokenB2B, 
            null,
            null,
            null
        );

        return $this->vaServices->doCheckStatusVa($header, $checkVARequestDto);
    }

    public function convertVAInquiryRequestSnapToV1Form($snapJson): string
    {
        return $this->vaServices->convertVAInquiryRequestSnapToV1Form($snapJson);
    }

    public function convertVAInquiryResponseV1XmlToSnapJson($xmlString): string
    {
        return $this->vaServices->convertVAInquiryResponseV1XmlToSnapJson($xmlString);
    }

    
}