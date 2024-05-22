<?php

class VaServices
{
    /**
     * Create a virtual account by making a request to the DOKU API
     *
     * @param CreateVaRequestDTO $requestDto The request DTO
     * @param string $accessToken The access token
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return CreateVaResponseDTO
     * @throws Exception If there is an error creating the virtual account
     */

    public function createVa(RequestHeaderDTO $requestHeaderDto, CreateVaRequestDTO $requestDto, bool $isProduction): CreateVaResponseDTO
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . CREATE_VA;
        $headerJson = json_encode($requestHeaderDto);
        $payloadJson = json_encode($requestDto);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerJson);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch); 
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['response_code']) && $responseData['response_code'] === '2007300') {
            $virtualAccountData = new VirtualAccountData(
                $responseData['virtual_account_no'],
                $responseData['virtual_account_name'],
                $responseData['virtual_account_email'],
                $responseData['virtual_account_phone'],
                $responseData['total_amount']['value'] ?? null,
                $responseData['total_amount']['currency'] ?? null,
                $responseData['expired_date'],
                $responseData['additional_info'] ?? []
            );
            return new CreateVaResponseDTO(
                $responseData['response_code'],
                $responseData['response_message'],
                $virtualAccountData
            );
        } else {
            throw new Exception('Error creating virtual account: ' . $responseData['response_message']);
        }
    }

    /**
     * Generate the external ID by combining the UUID and timestamp.
     *
     * @param string $timestamp The timestamp
     * @return string The generated external ID
     */
    public function generateExternalId(): string
    {
        // Generate a UUID and combine the UUID and timestamp
        $currentTimestamp = time();
        $formattedTimestamp = gmdate('Y-m-d\TH:i:s+07:00', $currentTimestamp);
        $uuid = bin2hex(random_bytes(16));
        $externalId = $uuid . $formattedTimestamp;

        return $externalId;
    }

    /**
     * Create the request header DTO for the create virtual account request.
     *
     * @param CreateVaRequestDTO $createVaRequestDto The create virtual account request DTO
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param string $tokenB2B The B2B token
     * @param string $timestamp The timestamp
     * @param string $externalId The external ID
     * @return RequestHeaderDTO The request header DTO
     */
    public function createVaRequestHeaderDto(
        CreateVaRequestDTO $createVaRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $timestamp,
        string $externalId,
        string $signature
    ): RequestHeaderDTO {
        $requestHeaderDto = new RequestHeaderDTO(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $createVaRequestDto->additionalInfo->channel,
            $tokenB2B
        );
        return $requestHeaderDto;
    }
}