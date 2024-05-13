<?php

require_once "src/commons/config.php";
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
            $formattedTimestamp = gmdate('YmdTHis.000\Z', $currentTimestamp);

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

        // Load the private key from a file (adjust this based on your implementation)
        $privateKeyResource = openssl_get_privatekey($privateKey);
        if ($privateKeyResource === false) {
            throw new Exception('Invalid privateKey format');
        }

        // Construct the string to sign
        $stringToSign = $clientId . '|' . $timestamp;

        // Generate the signature
        $signature = '';
        $success = openssl_sign($stringToSign, $signature, $privateKeyResource, 'SHA256');

        if (!$success) {
            throw new Exception('Failed to generate signature');
        }

        // Encode the signature in base64
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
        $apiEndpoint = getBaseURL($isProduction);
        $payload = [
            'signature' => $requestDto->signature,
            'timestamp' => $requestDto->timestamp,
            'client_id' => $requestDto->clientId,
            'grant_type' => $requestDto->grantType,
        ];

        $payloadStr = http_build_query($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
            throw new Exception('Get token http Error: ' . $responseData['response_message']);
        }
    }
}



