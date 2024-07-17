<?php
namespace Doku\Snap\Services;

use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\RequestHeader\RequestHeaderDTO;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\Request\CreateVaRequestDTO;
use Doku\Snap\Models\VA\Response\CreateVaResponseDTO;
use Doku\Snap\Models\Utilities\VirtualAccountData\CreateVaResponseVirtualAccountData;
use Doku\Snap\Models\Utilities\AdditionalInfo\CreateVaResponseAdditionalInfo;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDTO;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDTO;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\UpdateVaVirtualAccountConfig;
use Doku\Snap\Models\Utilities\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDTO;
use Doku\Snap\Models\VA\Response\DeleteVaResponseDTO;
use Doku\Snap\Models\Utilities\VirtualAccountData\DeleteVaResponseVirtualAccountData;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaResponseAdditionalInfo;  
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDTO;
use Doku\Snap\Models\VA\Response\CheckStatusVaResponseDTO;
use Doku\Snap\Models\Utilities\AdditionalInfo\CheckStatusResponseAdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountData\CheckStatusResponsePaymentFlagReason;
use Doku\Snap\Models\Utilities\VirtualAccountData\CheckStatusVirtualAccountData;

use Exception;
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
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CREATE_VA;
        $headers = Helper::prepareHeaders($requestHeaderDTO);
        
        $totalAmountArr = array(
            'value' => $requestDTO->totalAmount->value,
            'currency' => $requestDTO->totalAmount->currency
        );
        $virtualAccountConfigArr = array(
            'reusableStatus' => $requestDTO->additionalInfo->virtualAccountConfig->reusableStatus
        );
        $additionalInfoArr = array(
            'channel' => $requestDTO->additionalInfo->channel,
            'virtualAccountConfig' => $virtualAccountConfigArr,
            'origin' => $requestDTO->additionalInfo->origin->toArray()
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
            'expiredDate' => $requestDTO->expiredDate,
        );
        
        $payload = json_encode($payload);
        $response = Helper::doHitApi($apiEndpoint, $headers, $payload, "POST");
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2002700') {
            $responseData = $responseObject["virtualAccountData"];
            $totalAmount = new TotalAmount(
                $responseData['totalAmount']['value'] ?? null, 
                $responseData['totalAmount']['currency'] ?? null
            );
            $additionalInfo = new CreateVaResponseAdditionalInfo(
                $responseData['additionalInfo']['channel'] ?? null,
                $responseData['additionalInfo']['howToPayPage'] ?? null,
                $responseData['additionalInfo']['howToPayApi'] ?? null,
            );
            $virtualAccountData = new CreateVaResponseVirtualAccountData(
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

    public function doUpdateVa(RequestHeaderDTO $requestHeaderDto, UpdateVaRequestDTO $requestDTO, bool $isProduction = false): UpdateVaResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::UPDATE_VA_URL;
        $headers = Helper::prepareHeaders($requestHeaderDto);
        $payload = $requestDTO->getJSONRequestBody();
        $response = Helper::doHitApi($apiEndpoint, $headers, $payload, "PUT");
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2002800') {
            $responseData = $responseObject["virtualAccountData"];
            $totalAmount = new TotalAmount(
                $responseData['totalAmount']['value'] ?? null, 
                $responseData['totalAmount']['currency'] ?? null
            );
            $virtualAccountConfig = new UpdateVaVirtualAccountConfig(
                $responseData['additionalInfo']['virtualAccountConfig']['reusableStatus'] ?? null
            );
            $additionalInfo = new UpdateVaRequestAdditionalInfo(
                $responseData['additionalInfo']['channel'] ?? null,
                $virtualAccountConfig
            );
            $virtualAccountData = new UpdateVaRequestDTO(
                $responseData['partnerServiceId'],
                $responseData['customerNo'],
                $responseData['virtualAccountNo'],
                $responseData['virtualAccountName'],
                $responseData['virtualAccountEmail'],
                $responseData['virtualAccountPhone'],
                $responseData['trxId'],
                $totalAmount,
                $additionalInfo,
                $responseData['virtualAccountTrxType'],
                $responseData['expiredDate'],
            );
            return new UpdateVaResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $virtualAccountData
            );
        } else {
            throw new Exception('Error updating virtual account: ' . $responseObject['responseMessage']);
        }
    }

    public function doDeletePaymentCode(RequestHeaderDTO $requestHeader, DeleteVaRequestDTO $deleteVaRequest, bool $isProduction = false): DeleteVaResponseDTO
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DELETE_VA_URL;
        $headers = Helper::prepareHeaders($requestHeader);

        $payload = json_encode([
            'partnerServiceId' => $deleteVaRequest->partnerServiceId,
            'customerNo' => $deleteVaRequest->customerNo,
            'virtualAccountNo' => $deleteVaRequest->virtualAccountNo,
            'trxId' => $deleteVaRequest->trxId,
            'additionalInfo' => [
                'channel' => $deleteVaRequest->additionalInfo->channel
            ]
        ]);

        $response = Helper::doHitApi($apiEndpoint, $headers, $payload, "DELETE");
        $responseData = json_decode($response, true);

        if (isset($responseData['responseCode']) && $responseData['responseCode'] === '2003100') {
            return new DeleteVaResponseDto(
                $responseData['responseCode'],
                $responseData['responseMessage'] ?? '',
                new DeleteVaResponseVirtualAccountData(
                    $responseData['virtualAccountData']['partnerServiceId'] ?? '',
                    $responseData['virtualAccountData']['customerNo'] ?? '',
                    $responseData['virtualAccountData']['virtualAccountNo'] ?? '',
                    $responseData['virtualAccountData']['trxId'] ?? '',
                    new DeleteVaResponseAdditionalInfo(
                        $responseData['virtualAccountData']['additionalInfo']['channel'] ?? '',
                        $responseData['virtualAccountData']['additionalInfo']['virtualAccountConfig'] ?? ''
                    )
                )
            );
        } else {
            throw new Exception('Error deleting virtual account: ' . $responseData['responseMessage'] ?? $responseData['error']);
        }
    }

    public function doCheckStatusVa(RequestHeaderDTO $requestHeader, CheckStatusVaRequestDTO $checkStatusVaRequest, bool $isProduction = false): CheckStatusVaResponseDTO
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CHECK_VA;
        $headers = Helper::prepareHeaders($requestHeader);

        $payload = json_encode([
            'partnerServiceId' => $checkStatusVaRequest->partnerServiceId,
            'customerNo' => $checkStatusVaRequest->customerNo,
            'virtualAccountNo' => $checkStatusVaRequest->virtualAccountNo,
            'inquiryRequestId' => $checkStatusVaRequest->inquiryRequestId,
            'paymentRequestId' => $checkStatusVaRequest->paymentRequestId,
            'additionalInfo' => $checkStatusVaRequest->additionalInfo
        ]);

        $response = Helper::doHitApi($apiEndpoint, $headers, $payload, "POST");
        $responseData = json_decode($response, true);

        if (isset($responseData['responseCode']) && $responseData['responseCode'] === '2002600') {
            return new CheckStatusVaResponseDTO(
                $responseData['responseCode'],
                $responseData['responseMessage'] ?? '',
                new CheckStatusVirtualAccountData(
                    isset($responseData['virtualAccountData']['paymentFlagReason']) ? 
                        new CheckStatusResponsePaymentFlagReason(
                            $responseData['virtualAccountData']['paymentFlagReason']['english'] ?? '',
                            $responseData['virtualAccountData']['paymentFlagReason']['indonesia'] ?? ''
                        ) : null,
                    $responseData['virtualAccountData']['partnerServiceId'] ?? '',
                    $responseData['virtualAccountData']['customerNo'] ?? '',
                    $responseData['virtualAccountData']['virtualAccountNo'] ?? '',
                    $responseData['virtualAccountData']['inquiryRequestId'] ?? '',
                    $responseData['virtualAccountData']['paymentRequestId'] ?? '',
                    $responseData['virtualAccountData']['trxId'] ?? '',
                    new TotalAmount(
                        $responseData['virtualAccountData']['paidAmount']['value'] ?? 0,
                        $responseData['virtualAccountData']['paidAmount']['currency'] ?? ''
                    ),
                    new TotalAmount(
                        $responseData['virtualAccountData']['billAmount']['value'] ?? 0,
                        $responseData['virtualAccountData']['billAmount']['currency'] ?? ''
                    ),
                    new CheckStatusResponseAdditionalInfo(
                        $responseData['virtualAccountData']['additionalInfo']['acquirer'] ?? ''
                    )
                )
            );
        } else {
            throw new Exception('Error checking status of virtual account: ' . $responseData['responseMessage']);
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
        ?string $channelId,
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