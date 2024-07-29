<?php
namespace Doku\Snap\Services;

use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\Utilities\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\VA\Response\CreateVaResponseDto;
use Doku\Snap\Models\Utilities\VirtualAccountData\CreateVaResponseVirtualAccountData;
use Doku\Snap\Models\Utilities\AdditionalInfo\CreateVaResponseAdditionalInfo;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDto;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\UpdateVaVirtualAccountConfig;
use Doku\Snap\Models\Utilities\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\Response\DeleteVaResponseDto;
use Doku\Snap\Models\Utilities\VirtualAccountData\DeleteVaResponseVirtualAccountData;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaResponseAdditionalInfo;  
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Response\CheckStatusVaResponseDto;
use Doku\Snap\Models\Utilities\AdditionalInfo\CheckStatusResponseAdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountData\CheckStatusResponsePaymentFlagReason;
use Doku\Snap\Models\Utilities\VirtualAccountData\CheckStatusVirtualAccountData;

use Exception;
class VaServices
{
    public function createVa(RequestHeaderDto $requestHeaderDto, CreateVaRequestDto $requestDto, bool $isProduction): CreateVaResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CREATE_VA;
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $totalAmountArr = array(
            'value' => $requestDto->totalAmount->value,
            'currency' => $requestDto->totalAmount->currency
        );
        $virtualAccountConfigArr = array(
            'reusableStatus' => $requestDto->additionalInfo->virtualAccountConfig->reusableStatus
        );
        $additionalInfoArr = array(
            'channel' => $requestDto->additionalInfo->channel,
            'virtualAccountConfig' => $virtualAccountConfigArr,
            'origin' => $requestDto->additionalInfo->origin->toArray()
        );
        $payload = array(
            'partnerServiceId' => $requestDto->partnerServiceId,
            'customerNo' => $requestDto->customerNo,
            'virtualAccountNo' => $requestDto->virtualAccountNo,
            'virtualAccountName' => $requestDto->virtualAccountName,
            'virtualAccountEmail' => $requestDto->virtualAccountEmail,
            'virtualAccountPhone' => $requestDto->virtualAccountPhone,
            'trxId' => $requestDto->trxId,
            'totalAmount' => $totalAmountArr,
            'additionalInfo' => $additionalInfoArr,
            'virtualAccountTrxType' => $requestDto->virtualAccountTrxType,
            'expiredDate' => $requestDto->expiredDate,
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
            return new CreateVaResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $virtualAccountData
            );
        } else {
            throw new Exception('Error creating virtual account: ' . $responseObject['responseMessage']);
        }
    }

    public function doUpdateVa(RequestHeaderDto $requestHeaderDto, UpdateVaRequestDto $requestDto, bool $isProduction = false): UpdateVaResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::UPDATE_VA_URL;
        $headers = Helper::prepareHeaders($requestHeaderDto);
        $payload = $requestDto->generateJSONBody();
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
            $virtualAccountData = new UpdateVaRequestDto(
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

    public function doDeletePaymentCode(RequestHeaderDto $requestHeader, DeleteVaRequestDto $deleteVaRequest, bool $isProduction = false): DeleteVaResponseDto
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

    public function doCheckStatusVa(RequestHeaderDto $requestHeader, CheckStatusVaRequestDto $checkStatusVaRequest, bool $isProduction = false): CheckStatusVaResponseDto
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
            return new CheckStatusVaResponseDto(
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

    public function generateExternalId(): string
    {
        // Generate a UUID and combine the UUID and timestamp
        $uuid = bin2hex(random_bytes(16));
        $externalId = $uuid . Helper::getTimestamp();

        return $externalId;
    }

    public function generateRequestHeaderDto(
        string $timestamp,
        string $signature,
        string $clientId,
        string $externalId,
        ?string $channelId,
        string $tokenB2B
    ): RequestHeaderDto {
        $requestHeaderDto = new RequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $channelId,
            $tokenB2B
        );
        return $requestHeaderDto;
    }
}