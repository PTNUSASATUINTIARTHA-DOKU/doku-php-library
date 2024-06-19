<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require "src/commons/Helper.php";
require "src/commons/config.php";
require "src/models/request/TokenB2BRequestDTO.php";
require "src/models/response/TokenB2BResponseDTO.php";
class TokenServices
{
    private string $tokenB2B;
    private string $tokenExpiresIn;

    /**
     * Generate the timestamp in the required format for the DOKU SNAP API.
     *
     * @return string The timestamp in the format 'yyyyMMddTHH:mm:ss.SSSSZ'
     */
    public function getTimestamp(): string
    {
       return Helper::getTimestamp();
    }

    /**
     * Generate the X-SIGNATURE for the DOKU SNAP API request.
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param string $timestamp The timestamp for the request
     * @return string The generated X-SIGNATURE
     * @throws Exception If there is an error in signature generation
     */
    public function createSignature(string $privateKey, string $clientId, string $timestamp): string
    {
        // Validate the input parameters
        if (empty($privateKey) || empty($clientId) || empty($timestamp)) {
            throw new Exception('Invalid privateKey, clientId, or timestamp');
        }
        
        // Construct the string to sign and generate signature in base 64
        $stringToSign = $clientId . "|" . $timestamp;
        $signature = "";
        $success = openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$success) {
            throw new Exception('Failed to generate signature');
        }
        $base64Signature = base64_encode($signature);

        return $base64Signature;
    }

    /**
     * Create a TokenB2BRequestDTO instance
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @return TokenB2BRequestDTO
     * @throws Exception If there is an error creating the request DTO
     */
    public function createTokenB2BRequestDTO(string $signature, string $timestamp, string $clientId): TokenB2BRequestDTO
    {
        try {
            return new TokenB2BRequestDTO($signature, $timestamp, $clientId);
        } catch (Exception $e) {
            throw new Exception("Failed to generate TokenB2BRequestDTO: " . $e->getMessage());
        }
    }

    /**
     * Create a TokenB2B by making a request to the DOKU SNAP API
     *
     * @param TokenB2BRequestDTO $requestDTO The request DTO
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return TokenB2BResponseDTO
     * @throws Exception If there is an error creating the token
     */
    public function createTokenB2B(TokenB2BRequestDTO $requestDTO, bool $isProduction): TokenB2BResponseDTO
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . ACCESS_TOKEN;

        $headers = array(
            "X-CLIENT-KEY: " . $requestDTO->clientId,
            "X-TIMESTAMP: " .  $requestDTO->timestamp,
            "X-SIGNATURE: " . $requestDTO->signature,
            "Content-Type: " . "application/json"
        );

        $body = json_encode([
            'grantType' => 'client_credentials',
            'additionalInfo' => [],
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiEndpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
        ]);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData === null) {
            throw new Exception('Failed to decode JSON response');
        }

        if (isset($responseData['error'])) {
            throw new Exception($responseData['error']['message'] ?? 'Missing error in response data');
        }

        try {
            return new TokenB2BResponseDTO(
                $responseData['responseCode'] ?? '',
                $responseData['responseMessage'] ?? '',
                $responseData['accessToken'] ?? '',
                $responseData['tokenType'] ?? '',
                $responseData['expiresIn'] ?? '',
                $responseData['additionalInfo'] ?? ''
            );
        } catch (Error $e) {
            throw new Exception('Failed to create TokenB2BResponseDTO: ' . $e->getMessage());
        }
    }

    /**
     * Checking if a generated TokenB2B is empty or not.
     *
     * @param string $tokenB2B The generated tokenB2B
     * @return bool true if tokenB2B isEmpty, else false
     * @throws Exception If there is an error creating the token
     */
    public function isTokenEmpty(string $tokenB2B): bool
    {
        if(is_null($tokenB2B)) {
            return true;
        }
        return false;
    }

    /**
     * Checking if a generated TokenB2B has been already expired or not
     *
     * @param string $tokenB2B The generated tokenB2B
     * @return bool true if tokenB2B is already expired, else false
     * @throws Exception If there is an error creating the token
     */
    public function isTokenExpired(int $tokenExpiresIn, int $tokenGeneratedTimestamp) 
    {
        $currentTimestamp = time();
        $expirationTimestamp = $tokenGeneratedTimestamp + $tokenExpiresIn;
        
        // Check if the token has expired
        return $expirationTimestamp < $currentTimestamp;
    }

    /**
     * Compare the request signature with the created signature.
     *
     * @param string $requestSignature The signature received in the request
     * @param string $createdSignature The signature generated by the application
     *
     * @return bool True if the signatures match, false otherwise
     */
    public function compareSignatures(string $requestSignature, string $createdSignature)
    {
        if ($requestSignature === $createdSignature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate an invalid signature response.
     *
     * @param string $timestamp
     * @return NotificationTokenBodyDTO
     */
    public function generateInvalidSignature(string $timestamp): NotificationTokenDTO
    {
        $responseHeader = new NotificationTokenHeaderDTO(
            null,
            $timestamp
        );

        $responseBody = new NotificationTokenBodyDTO(
            "4017300",
            "Unauthorized. Invalid Signature",
            null,
            null,
            null,
            null
        );

        $response = new NotificationTokenDTO(
            $responseHeader,
            $responseBody
        );

        return $response;
    }

    /**
     * Generate a NotificationTokenDTO with the provided parameters.
     *
     * @param string $token The access token
     * @param string $timestamp The timestamp
     * @param string $clientId The client ID
     * @param int $expiresIn The expiration time (in seconds) for the access token
     *
     * @return NotificationTokenDTO
     */
    public function generateNotificationTokenDTO(string $token, string $timestamp, string $clientId, int $expiresIn): NotificationTokenDTO
    {
        $responseBody = new NotificationTokenBodyDTO(
            "2007300",
            "Successful",
            $token,
            "Bearer",
            $expiresIn,
            ""
        );

        $responseBody->timestamp = $timestamp;
        $responseBody->clientKey = $clientId;

        $header = new NotificationTokenHeaderDTO(
            $clientId,
            $timestamp
        );

        return new NotificationTokenDTO(
            $header,
            $responseBody
        );
    }

    /**
     * Validate the TokenB2B received in a payment notification HTTP request
     *
     * @param string $jwtToken The JWT token received in the request
     * @param string $publicKey The public key used for token verification
     * @return bool True if the token is valid, false otherwise
     */
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

    /**
     * Generate a JWT token
     *
     * @param int $expiredIn Timestamp when the token should expire
     * @param string $issuer The issuer of the token
     * @param string $privateKey The private key for signing the token
     * @param string $clientId The client ID to include in the token
     * @return string The generated JWT token
     * @throws Exception If there is an error generating the token
     */
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

}