<?php

class VaController
{
    private $vaServices;
    private $tokenServices;
    public function __construct() {
        $this->vaServices = new VaServices();
        $this->tokenServices = new TokenServices();
    }
    public function createVa(CreateVaRequestDto $createVaRequestDto, string $privateKey, string $clientId, string $tokenB2B, bool $isProduction): CreateVaResponseDTO
    {
        $externalId = $this->vaServices->generateExternalId();
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);
        $requestHeaderDto = $this->vaServices->createVaRequestHeaderDto($createVaRequestDto, $privateKey, $clientId, $tokenB2B, $timestamp, $externalId, $signature);
        $createVaResponseDto = $this->vaServices->createVa($requestHeaderDto, $createVaRequestDto, $isProduction);
        return $createVaResponseDto;
    }
}