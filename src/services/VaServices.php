<?php
require "src/models/request/RequestHeaderDTO.php";
require "src/models/va/utility/VaResponseVirtualAccountData.php";
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

    public function createVa(RequestHeaderDTO $requestHeaderDTO, VaRequestDTO $createRequestDTO, bool $isProduction): CreateVaResponseDTO
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . CREATE_VA;
        $headers = $this->prepareHeaders($requestHeaderDTO);
        $payload = json_encode($this->preparePayload($createRequestDTO, true));
        $response = $this->doHitAPI($apiEndpoint, $payload, "POST");
        $responseObject = json_decode($response, true);

        return $this->constructVAResponseDTO($responseObject);
    }

    public function doUpdateVa(RequestHeaderDTO $requestHeaderDto, VaRequestDTO $updateVaRequestDTO, bool $isProduction = false): UpdateVaResponseDto
    {
        $baseUrl = getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . UPDATE_VA_URL;
        $headers = $this->prepareHeaders($requestHeaderDto);
        $payload = json_encode($this->preparePayload($updateVaRequestDTO, false));
        $response = $this->doHitAPI($apiEndpoint, $payload, "PUT");
        $responseBody = json_decode($response, true);
        return $this->constructVAResponseDTO($responseBody);
    }



    private function prepareHeaders(RequestHeaderDTO $requestHeaderDTO): array
    {
        return array(
            "Content-Type: application/json",
            'X-PARTNER-ID: ' . $requestHeaderDTO->xPartnerId,
            'X-EXTERNAL-ID: ' . $requestHeaderDTO->xRequestId,
            'X-TIMESTAMP: ' . $requestHeaderDTO->xTimestamp,
            'X-SIGNATURE: ' . $requestHeaderDTO->xSignature,
            'Authorization: Bearer ' . $requestHeaderDTO->authorization,
            'CHANNEL-ID: ' . $requestHeaderDTO->channelId
        );
    }

    /**
     * Prepare the payload for creating or updating a virtual account.
     *
     * @param VaRequestDTO $requestDTO The request data transfer object.
     * @param int $requestFlag Flag indicating whether it's for creating (0) or updating (1) a virtual account.
     * @return array The prepared payload array.
     */
    private function preparePayload(VaRequestDTO $requestDTO, int $requestFlag = 0): array
    {
        $totalAmountArr = array(
            'value' => $requestDTO->totalAmount->value,
            'currency' => $requestDTO->totalAmount->currency
        );
        $virtualAccountConfigArr = null;
        if($requestFlag === 0) {
            $virtualAccountConfigArr = array(
                'reusableStatus' => $requestDTO->additionalInfo->virtualAccountConfig->reusableStatus
            );
        } else {
            $virtualAccountConfigArr = array(
                'status' => $requestDTO->additionalInfo->virtualAccountConfig->status
            );  
        }
        $additionalInfoArr = array(
            'channel' => $requestDTO->additionalInfo->channel,
            'virtualAccountConfig' => $virtualAccountConfigArr
        );
        return array(
            'partnerServiceId' => $requestDTO->partnerServiceId,
            'customerNo' => $requestDTO->customerNo,
            'virtualAccountNo' => $requestDTO->virtualAccountNo,
            'virtualAccountName' => $requestDTO->virtualAccountName,
            'virtualAccountEmail' => $requestDTO->virtualAccountEmail,
            'virtualAccountPhone' => $requestDTO->virtualAccountPhone,
            'trxId' => $requestDTO->trxId,
            'totalAmount' => $totalAmountArr,
            'additionalInfo' => $additionalInfoArr,
            'virtualAccountTrxType' => $requestDTO->virtualAccountTrxType,
            'expiredDate' => $requestDTO->expiredDate,
        );
    }

    private function doHitAPI(string $apiEndpoint, string $payload, string $customRequest = "POST"): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customRequest);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch); 
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);
        return $response;
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
    public function generateRequestHeaderDTO(
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