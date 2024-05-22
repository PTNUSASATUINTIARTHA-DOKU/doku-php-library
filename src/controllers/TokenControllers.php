<?php

require_once 'src/services/TokenServices.php';

class TokenController
{
    private $tokenServices;

    public function __construct()
    {
        $this->tokenServices = new TokenServices();
    }

    /**
     * Generate a TokenB2BResponseDto by following the pipeline
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return TokenB2BResponseDTO
     * @throws Exception If any step in the pipeline fails
     */
    public function getTokenB2B(string $privateKey, string $clientId, bool $isProduction): TokenB2BResponseDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);
        $tokenB2BRequestDto = $this->tokenServices->createTokenB2BRequestDTO($signature, $timestamp, $clientId);
        $tokenB2BResponseDto = $this->tokenServices->createTokenB2B($tokenB2BRequestDto, $isProduction);
        return $tokenB2BResponseDto;
    }

    /**
     * Check validity of a TokenB2BResponseDto by following the pipeline
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return TokenB2BResponseDTO
     * @throws Exception If any step in the pipeline fails
     */
    public function isTokenInvalid(string $tokenB2B, string $tokenExpiresIn, string $tokenGeneratedTimestamp): bool
    {
        if($this->tokenServices->isTokenEmpty($tokenB2B)){
            return true;
        } else {
            if($this->tokenServices->isTokenExpired($tokenExpiresIn, $tokenGeneratedTimestamp)){
                return true;
            } else {
                return false;
            }
        }
    }
}