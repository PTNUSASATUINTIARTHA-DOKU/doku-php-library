<?php
namespace Doku\Snap\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Error;

use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\Token\TokenB2BRequestDto;
use Doku\Snap\Models\Token\TokenB2BResponseDto;
use Doku\Snap\Models\Token\TokenB2B2CRequestDto;
use Doku\Snap\Models\Token\TokenB2B2CResponseDto;
use Doku\Snap\Models\Notification\NotificationTokenDto;
use Doku\Snap\Models\Notification\NotificationTokenHeaderDto;
use Doku\Snap\Models\Notification\NotificationTokenBodyDto;

class TokenServices
{
    private string $tokenB2B;
    private string $tokenExpiresIn;

    public function getTimestamp(): string
    {
       return Helper::getTimestamp();
    }

    public function createSignature(string $privateKey, string $clientId, string $timestamp): string
    {
        if (empty($privateKey) || empty($clientId) || empty($timestamp)) {
            throw new Exception('Invalid privateKey, clientId, or timestamp');
        }
        $stringToSign = $clientId . "|" . $timestamp;
        $signature = "";
        $success = openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$success) {
            throw new Exception('Failed to generate signature');
        }
        $base64Signature = base64_encode($signature);

        return $base64Signature;
    }

    public function createTokenB2BRequestDto(string $signature, string $timestamp, string $clientId): TokenB2BRequestDto
    {
        try {
            return new TokenB2BRequestDto($signature, $timestamp, $clientId);
        } catch (Exception $e) {
            throw new Exception("Failed to generate TokenB2BRequestDto: " . $e->getMessage());
        }
    }

    public function createTokenB2B(TokenB2BRequestDto $requestDto, bool $isProduction): TokenB2BResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::ACCESS_TOKEN;

        $headers = array(
            "X-CLIENT-KEY: " . $requestDto->clientId,
            "X-TIMESTAMP: " .  $requestDto->timestamp,
            "X-SIGNATURE: " . $requestDto->signature,
            "Content-Type: " . "application/json"
        );

        $body = json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $body, 'POST');
        $responseData = json_decode($response, true);

        if ($responseData === null) {
            throw new Exception('Null Response Data: Failed to decode JSON response');
        }

        if (isset($responseData['error'])) {
            print_r($responseData['error']);
            throw new Exception('Missing error in response data');
        }

        try {
            return new TokenB2BResponseDto(
                $responseData['responseCode'] ?? '',
                $responseData['responseMessage'] ?? '',
                $responseData['accessToken'] ?? '',
                $responseData['tokenType'] ?? '',
                $responseData['expiresIn'] ?? '',
                $responseData['additionalInfo'] ?? ''
            );
        } catch (Error $e) {
            throw new Exception('Failed to create TokenB2BResponseDto: ' . $e->getMessage());
        }
    }

    public function createTokenB2B2CRequestDto(string $authCode): TokenB2B2CRequestDto
    {
        try {
            return new TokenB2B2CRequestDto("authorization_code", $authCode);
        } catch (Exception $e) {
            throw new Exception("Failed to generate TokenB2B2CRequestDto: " . $e->getMessage());
        }
    }

    public function hitTokenB2B2CApi(TokenB2B2CRequestDto $tokenB2B2CRequestDto, string $timestamp, string $signature, string $clientId, bool $isProduction): TokenB2B2CResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::ACCESS_TOKEN_B2B2C;

        $headers = array(
            "X-CLIENT-KEY: " . $clientId,
            "X-TIMESTAMP: " . $timestamp,
            "X-SIGNATURE: " . $signature,
            "Content-Type: application/json"
        );

        $body = json_encode([
            'grantType' => $tokenB2B2CRequestDto->grantType,
            'authCode' => $tokenB2B2CRequestDto->authCode,
            'additionalInfo' => $tokenB2B2CRequestDto->additionalInfo,
        ]);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $body, 'POST');

        $responseData = json_decode($response, true);

        if ($responseData === null) {
            throw new Exception('Null Response Data: Failed to decode JSON response');
        }

        if (isset($responseData['error'])) {
            print_r($responseData['error']);
            throw new Exception('Error in response data');
        }

        try {
            return new TokenB2B2CResponseDto(
                $responseData['responseCode'] ?? '',
                $responseData['responseMessage'] ?? '',
                $responseData['accessToken'] ?? '',
                $responseData['tokenType'] ?? '',
                $responseData['accessTokenExpiryTime'] ?? '',
                $responseData['refreshToken'] ?? '',
                $responseData['refreshTokenExpiryTime'] ?? '',
                $responseData['additionalInfo'] ?? null
            );
        } catch (Error $e) {
            throw new Exception('Failed to create TokenB2B2CResponseDto: ' . $e->getMessage());
        }
    }

    public function isTokenEmpty(string $tokenB2B): bool
    {
        if(is_null($tokenB2B)) {
            return true;
        }
        return false;
    }

    public function isTokenExpired(int $tokenExpiresIn, int $tokenGeneratedTimestamp) 
    {
        $currentTimestamp = time();
        $expirationTimestamp = $tokenGeneratedTimestamp + $tokenExpiresIn;
        return $expirationTimestamp < $currentTimestamp;
    }

    public function compareSignatures(string $requestSignature, string $createdSignature)
    {
        if ($requestSignature === $createdSignature) {
            return true;
        } else {
            return false;
        }
    }

    public function generateInvalidSignature(string $timestamp): NotificationTokenDto
    {
        $responseHeader = new NotificationTokenHeaderDto(
            null,
            $timestamp
        );

        $responseBody = new NotificationTokenBodyDto(
            "4017300",
            "Unauthorized. Invalid Signature",
            null,
            null,
            null,
            null
        );

        $response = new NotificationTokenDto(
            $responseHeader,
            $responseBody
        );

        return $response;
    }

    public function generateNotificationTokenDto(string $token, string $timestamp, string $clientId, int $expiresIn): NotificationTokenDto
    {
        $responseBody = new NotificationTokenBodyDto(
            "2007300",
            "Successful",
            $token,
            "Bearer",
            $expiresIn,
            ""
        );

        $responseBody->timestamp = $timestamp;
        $responseBody->clientKey = $clientId;

        $header = new NotificationTokenHeaderDto(
            $clientId,
            $timestamp
        );

        return new NotificationTokenDto(
            $header,
            $responseBody
        );
    }

    public function validateTokenB2B(string $jwtToken, string $publicKey): bool
    {
        try {
            // object, not boolean
            $decodedToken = JWT::decode($jwtToken, new Key($publicKey, 'RS256'));
            // access decoded token if needed: decodedToken->iss, $decodedToken->exp, etc.
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function generateToken(int $expiredIn, string $issuer, string $privateKey, string $clientId): string
    {
        $issuedAt = time();
        $expiredAt = $issuedAt + $expiredIn;

        $payload = [
            'iss' => $issuer,
            'iat' => $issuedAt,
            'exp' => $expiredAt,
            'clientId' => $clientId,
        ];

        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
    }

    public function generateSymmetricSignature(
        string $httpMethod,
        string $endpointUrl,
        string $tokenB2B,
        string $requestBody,
        string $timestamp,
        string $secretKey
    ): string {
        $minifiedBody = json_encode(json_decode($requestBody), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $bodyHash = hash('sha256', $minifiedBody);
        $bodyHashHex = strtolower($bodyHash);
        $stringToSign = $httpMethod . ":" . $endpointUrl . ":" . $tokenB2B . ":" . $bodyHashHex . ":" . $timestamp;
        $signature = hash_hmac('sha512', $stringToSign, $secretKey, true);
        return base64_encode($signature);
    }

    public function generateAsymmetricSignature(
        string $privateKey,
        string $clientId,
        string $timestamp
    ): string {
        $stringToSign = $clientId . "|" . $timestamp;
        $signature = hash_hmac('sha512', $stringToSign, $privateKey, true);
        return base64_encode($signature);
    }
}