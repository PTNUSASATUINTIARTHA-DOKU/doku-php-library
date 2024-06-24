<?php
require "src/models/request/RequestHeaderDTO.php";
require "src/models/va/VirtualAccountData.php";
require "src/models/response/CreateVAResponseDTO.php";
class VaServices
{
    /**
     * Create a virtual account by making a request to the DOKU API
     *
     * @param CreateVaRequestDTO $requestDTO The request DTO
     * @param string $accessToken The access token
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return CreateVaResponseDTO
     * @throws Exception If there is an error creating the virtual account
     */

    public function createVa(RequestHeaderDTO $requestHeaderDTO, CreateVaRequestDTO $requestDTO, bool $isProduction): CreateVaResponseDTO
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . CREATE_VA;
        $headers = $this->prepareHeaders($requestHeaderDTO);
        $payload = json_encode($this->preparePayload($requestDTO));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch); 
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);

        $responseObject = json_decode($response, true);

        return $this->constructVAResponseDTO($responseObject);
    }

    public function doUpdateVa(RequestHeaderDTO $requestHeaderDto, UpdateVaDTO $updateVaDto, bool $isProduction = false): UpdateVaResponseDto
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . UPDATE_VA_URL;
        $headers = $this->prepareHeaders($requestHeaderDto);
        $payload = json_encode($this->preparePayload($updateVaDto));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            return new UpdateVaResponseDto('500', 'Curl error: ' . curl_error($ch), null);
        }

        curl_close($ch);

        $responseBody = json_decode($response, true);
        return $this->constructVAResponseDTO($responseBody); // TODO 
    }

    private function prepareHeaders(RequestHeaderDTO $requestHeaderDto): array
    {
        return [
            'Content-Type' => 'application/json',
            'X-TIMESTAMP' => $requestHeaderDto->xTimestamp,
            'X-SIGNATURE' => $requestHeaderDto->xSignature,
            'X-PARTNER-ID' => $requestHeaderDto->xPartnerId,
            'X-REQUEST-ID' => $requestHeaderDto->xRequestId,
            'CHANNEL-ID' => $requestHeaderDto->channelId,
            'Authorization' => $requestHeaderDto->authorization,
        ];
    }

    private function preparePayload( $updateVaDto): array
    {
        return [
            'partnerServiceId' => $updateVaDto->partnerServiceId,
            'customerNo' => $updateVaDto->customerNo,
            'virtualAccountNo' => $updateVaDto->virtualAccountNo,
            'virtualAccountName' => $updateVaDto->virtualAccountName,
            'virtualAccountEmail' => $updateVaDto->virtualAccountEmail,
            'virtualAccountPhone' => $updateVaDto->virtualAccountPhone,
            'trxId' => $updateVaDto->trxId,
            'totalAmount' => [
                'value' => $updateVaDto->totalAmount->value,
                'currency' => $updateVaDto->totalAmount->currency,
            ],
            'additionalInfo' => [
                'channel' => $updateVaDto->additionalInfo->channel,
                'virtualAccountConfig' => [
                    'status' => $updateVaDto->additionalInfo->virtualAccountConfig->status,
                ],
            ],
            'virtualAccountTrxType' => $updateVaDto->virtualAccountTrxType,
            'expiredDate' => $updateVaDto->expiredDate,
        ];
    }

    private function constructVAResponseDTO($responseObject): CreateVaResponseDTO
    {
        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2002700') {
            $responseData = $responseObject["virtualAccountData"];
            $totalAmount = new TotalAmount(
                $responseData['totalAmount']['value'] ?? null, 
                $responseData['totalAmount']['currency'] ?? null
            );
            $virtualAccountConfig = new VirtualAccountConfig(
                $responseData['additionalInfo']['virtualAccountConfig']['reusableStatus'] ?? null
            );
            $additionalInfo = new AdditionalInfo(
                $responseData['additionalInfo']['channel'] ?? null,
                $virtualAccountConfig
            );
            $virtualAccountData = new VirtualAccountData(
                $responseData['partnerServiceId'],
                $responseData['customerNo'],
                $responseData['virtualAccountNo'],
                $responseData['virtualAccountName'],
                $responseData['virtualAccountEmail'],
                $responseData['trxId'],
                $totalAmount,
                $additionalInfo
            );
            return new CreateVaResponseDTO(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $virtualAccountData
            );
        } else {
            throw new Exception('Error creating virtual account: ' . $responseObject['responseMessage']);
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
        $uuid = bin2hex(random_bytes(16));
        $externalId = $uuid . Helper::getTimestamp();

        return $externalId;
    }

    /**
     * Create the request header DTO for the create virtual account request.
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param string $tokenB2B The B2B token
     * @param string $timestamp The timestamp
     * @param string $externalId The external ID
     * @return RequestHeaderDTO The request header DTO
     */
    public function createVaRequestHeaderDTO(
        string $timestamp,
        string $signature,
        string $clientId,
        string $externalId,
        string $channelId,
        string $tokenB2B
    ): RequestHeaderDTO {
        $requestHeaderDTO = new RequestHeaderDTO(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $channelId,
            $tokenB2B
        );
        return $requestHeaderDTO;
    }
}