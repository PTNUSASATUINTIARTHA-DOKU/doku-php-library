<?php
namespace Doku\Snap\Controllers;

use Exception;

use Doku\Snap\Services\TokenServices;
use Doku\Snap\Services\VaServices;
use Doku\Snap\Models\Token\TokenB2BResponseDTO;
use Doku\Snap\Models\RequestHeader\RequestHeaderDTO;
use Doku\Snap\Models\Notification\NotificationTokenDTO;

class TokenController
{
    private TokenServices $tokenServices;
    private VaServices $vaServices;

    public function __construct()
    {
        $this->tokenServices = new TokenServices();
        $this->vaServices = new VaServices();
    }

    /**
     * Get a TokenB2BResponseDTO from backend by following the pipeline
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
        $tokenB2BRequestDTO = $this->tokenServices->createTokenB2BRequestDTO($signature, $timestamp, $clientId);
        $tokenB2BResponseDTO = $this->tokenServices->createTokenB2B($tokenB2BRequestDTO, $isProduction);
        return $tokenB2BResponseDTO;
    } 

    /**
     * Check validity of a TokenB2BResponseDTO by following the pipeline
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return TokenB2BResponseDTO
     * @throws Exception If any step in the pipeline fails
     */
    public function isTokenInvalid(string $tokenB2B, int $tokenExpiresIn, int $tokenGeneratedTimestamp): bool
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

    /**
     * Validate the request signature against the signature generated from the provided parameters.
     *
     * @param string $requestSignature The signature received in the request
     * @param string $requestTimestamp The timestamp received in the request
     * @param string $privateKey The private key used for signature generation
     * @param string $clientId The client ID used for signature generation
     *
     * @return bool True if the signatures match, false otherwise
     */
    public function validateSignature(string $requestSignature, string $requestTimestamp, string $privateKey, string $clientId): bool
    {
        $createdSignature = $this->tokenServices->createSignature($requestTimestamp, $privateKey, $clientId);

        return $this->tokenServices->compareSignatures($requestSignature, $createdSignature);
    }

    /**
     * Generate a response for invalid signature.
     *
     * @return NotificationTokenDTO
     */
    public function generateInvalidSignatureResponse(): NotificationTokenDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        return $this->tokenServices->generateInvalidSignature($timestamp);
    }

    /**
     * Validate the TokenB2B received in a payment notification HTTP request
     *
     * @param string $jwtToken The JWT token received in the request
     * @param string $publicKey The public key used for token verification
     * @return bool True if the token is valid, false otherwise
     */
    public function validateTokenB2B(string $requestTokenB2B, string $publicKey): bool 
    {
        return $this->tokenServices->validateTokenB2b($requestTokenB2B, $publicKey);
    }

    /**
     * Generates a TokenB2B token with the given expiration time, issuer, private key, and client ID.
     *
     * @param int $expiredIn The expiration time of the token in seconds.
     * @param string $issuer The issuer of the token.
     * @param string $privateKey The private key used for signing the token.
     * @param string $clientId The client ID to include in the token.
     * @return NotificationTokenDTO The generated TokenB2B tokenDTO.
     */
    public function generateTokenB2B(int $expiredIn, string $issuer, string $privateKey, string $clientId): NotificationTokenDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $token = $this->tokenServices->generateToken($expiredIn, $issuer, $privateKey, $clientId);
        $notificationTokenDTO = $this->tokenServices->generateNotificationTokenDTO($token, $timestamp, $clientId, $expiredIn);
        return $notificationTokenDTO;
    }

    /**
     * Generates a request header DTO with the given private key, client ID, token B2B, and channel ID.
     *
     * @param string $privateKey The private key used for signing the request header.
     * @param string $clientId The client ID to include in the request header.
     * @param string $tokenB2B The token B2B to include in the request header.
     * @param string $channelId The channel ID to include in the request header.
     * @return RequestHeaderDTO The generated request header DTO.
     */
    public function doGenerateRequestHeader(string $privateKey, string $clientId, string $tokenB2B, string $channelId = "SDK"): RequestHeaderDTO
    {
        $externalId = $this->vaServices->generateExternalId();
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);

        return $this->vaServices->generateRequestHeaderDTO(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $channelId,
            $tokenB2B
        );
    }

}

