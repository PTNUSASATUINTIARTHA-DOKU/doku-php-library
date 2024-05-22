<?php

require_once "src/commons/config.php";
require_once "src/models/TokenB2BRequestDTO.php";
class TokenServices
{
    private $tokenB2B;
    private $tokenExpiresIn;
    private $tokenTimestamp;

    /**
     * Generate the timestamp in the required format for the DOKU SNAP API.
     *
     * @return string The timestamp in the format 'yyyyMMddTHH:mm:ss.SSSSZ'
     */
    public function getTimestamp(): string
    {
        try {
            $currentTimestamp = time();
            $formattedTimestamp = gmdate('Y-m-d\TH:i:s+07:00', $currentTimestamp);
            return $formattedTimestamp;
        } catch (Exception $e) {
            throw new Exception("Failed to generate timestamp: " . $e->getMessage());
        }
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
        $stringToSign = $clientId . '|' . $timestamp;
        $signature = '';
        $success = openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (empty($signature) || !$success) {
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
     * @param TokenB2BRequestDTO $requestDto The request DTO
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return TokenB2BResponseDTO
     * @throws Exception If there is an error creating the token
     */
    public function createTokenB2B(TokenB2BRequestDTO $requestDto, bool $isProduction): TokenB2BResponseDTO
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . ACCESS_TOKEN;

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

        if (isset($responseData['response_code']) && $responseData['response_code'] === '200') {
            return new TokenB2BResponseDTO(
                $responseData['response_code'],
                $responseData['response_message'],
                $responseData['access_token'],
                $responseData['token_type'],
                $responseData['expires_in'],
                $responseData['additional_info'] ?? ''
            );
        } else {
            throw new Exception('Get token http Error: ' . implode(',',$responseData));
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
            return false;
        }
        return true;
    }

    /**
     * Checking if a generated TokenB2B has been already expired or not
     *
     * @param string $tokenB2B The generated tokenB2B
     * @return bool true if tokenB2B is already expired, else false
     * @throws Exception If there is an error creating the token
     */
    public function isTokenExpired($tokenExpiresIn, $tokenGeneratedTimestamp) {
        $currentTimestamp = time();
        $expirationTimestamp = $tokenGeneratedTimestamp + $tokenExpiresIn;
        
        // Check if the token has expired
        return $expirationTimestamp < $currentTimestamp;
    }
}



