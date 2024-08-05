<?php
namespace Doku\Snap\Controllers;

use Exception;

use Doku\Snap\Services\TokenServices;
use Doku\Snap\Services\VaServices;
use Doku\Snap\Models\Token\TokenB2BResponseDto;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\Notification\NotificationTokenDto;
use Doku\Snap\Commons\Helper;

class TokenController
{
    private TokenServices $tokenServices;
    private VaServices $vaServices;

    public function __construct()
    {
        $this->tokenServices = new TokenServices();
        $this->vaServices = new VaServices();
    }

    public function getTokenB2B(string $privateKey, string $clientId, bool $isProduction): TokenB2BResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);
        $tokenB2BRequestDto = $this->tokenServices->createTokenB2BRequestDto($signature, $timestamp, $clientId);
        $tokenB2BResponseDto = $this->tokenServices->createTokenB2B($tokenB2BRequestDto, $isProduction);
        return $tokenB2BResponseDto;
    } 

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

    public function validateSignature(string $requestSignature, string $requestTimestamp, string $privateKey, string $clientId): bool
    {
        $createdSignature = $this->tokenServices->createSignature($privateKey, $clientId, $requestTimestamp);

        return $this->tokenServices->compareSignatures($requestSignature, $createdSignature);
    }

    public function generateInvalidSignatureResponse(): NotificationTokenDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        return $this->tokenServices->generateInvalidSignature($timestamp);
    }

    public function validateTokenB2B(string $requestTokenB2B, string $publicKey): bool 
    {
        return $this->tokenServices->validateTokenB2b($requestTokenB2B, $publicKey);
    }

    public function generateTokenB2B(int $expiredIn, string $issuer, string $privateKey, string $clientId): NotificationTokenDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $token = $this->tokenServices->generateToken($expiredIn, $issuer, $privateKey, $clientId);
        $notificationTokenDto = $this->tokenServices->generateNotificationTokenDto($token, $timestamp, $clientId, $expiredIn);
        return $notificationTokenDto;
    }

    public function doGenerateRequestHeader(string $privateKey, string $clientId, string $tokenB2B, string $channelId = "SDK"): RequestHeaderDto
    {
        $externalId = Helper::generateExternalId();;
        $timestamp = $this->tokenServices->getTimestamp();
        $signature = $this->tokenServices->createSignature($privateKey, $clientId, $timestamp);

        return Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $channelId,
            $tokenB2B
        );
    }

}

