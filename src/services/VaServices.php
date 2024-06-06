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
        $header = array(
            "Content-Type: application/json",
            'X-PARTNER-ID: ' . $requestHeaderDTO->xPartnerId,
            'X-EXTERNAL-ID: ' . $requestHeaderDTO->xRequestId,
            'X-TIMESTAMP: ' . $requestHeaderDTO->xTimestamp,
            'X-SIGNATURE: ' . $requestHeaderDTO->xSignature,
            'Authorization:Bearer ' . $requestHeaderDTO->authorization,
            'CHANNEL-ID: ' . $requestHeaderDTO->channelId
        );
        $totalAmountArr = array(
            'value' => $requestDTO->totalAmount->value,
            'currency' => $requestDTO->totalAmount->currency
        );
        $virtualAccountConfigArr = array(
            'reusableStatus' => $requestDTO->additionalInfo->virtualAccountConfig->reusableStatus
        );
        $additionalInfoArr = array(
            'channel' => $requestDTO->additionalInfo->channel,
            'virtualAccountConfig' => $virtualAccountConfigArr
        );
        $payload = array(
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
            'expiredDate' => $requestDTO->expiredDate
        );

        $payload = json_encode($payload);

        print_r($payload);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch); 
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);

        $responseObject = json_decode($response, true);
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
     * @param CreateVaRequestDTO $createVaRequestDTO The create virtual account request DTO
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param string $tokenB2B The B2B token
     * @param string $timestamp The timestamp
     * @param string $externalId The external ID
     * @return RequestHeaderDTO The request header DTO
     */
    public function createVaRequestHeaderDTO(
        CreateVaRequestDTO $createVaRequestDTO,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $timestamp,
        string $externalId,
        string $signature
    ): RequestHeaderDTO {
        $requestHeaderDTO = new RequestHeaderDTO(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $createVaRequestDTO->additionalInfo->channel,
            $tokenB2B
        );
        return $requestHeaderDTO;
    }
}