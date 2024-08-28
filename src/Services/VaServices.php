<?php
namespace Doku\Snap\Services;

use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Commons\VaChannels;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\VA\Response\CreateVaResponseDto;
use Doku\Snap\Models\VA\VirtualAccountData\CreateVaResponseVirtualAccountData;
use Doku\Snap\Models\VA\AdditionalInfo\CreateVaResponseAdditionalInfo;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDto;
use Doku\Snap\Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig;
use Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\Response\DeleteVaResponseDto;
use Doku\Snap\Models\VA\VirtualAccountData\DeleteVaResponseVirtualAccountData;
use Doku\Snap\Models\VA\AdditionalInfo\DeleteVaResponseAdditionalInfo;  
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Response\CheckStatusVaResponseDto;
use Doku\Snap\Models\VA\AdditionalInfo\CheckStatusResponseAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountData\CheckStatusResponsePaymentFlagReason;
use Doku\Snap\Models\VA\VirtualAccountData\CheckStatusVirtualAccountData;

use Exception;
class VaServices
{
    public function createVa(RequestHeaderDto $requestHeaderDto, CreateVaRequestDto $requestDto, bool $isProduction): CreateVaResponseDto
    {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CREATE_VA;
        $headers = Helper::prepareHeaders($requestHeaderDto);
        $payload = $requestDto->generateJSONBody();
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
                $responseData['virtualAccountTrxType'],
                $responseData['expiredDate'],
                $additionalInfo
            );
            return new CreateVaResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $virtualAccountData
            );
        } else {
             return new CreateVaResponseDto(
                $responseObject['responseCode'],
                'Error creating virtual account: ' . $responseObject['responseMessage'],
                null
            );
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
            return new UpdateVaResponseDto(
                $responseObject['responseCode'],
                'Error updating virtual account: ' . $responseObject['responseMessage'],
                null
            );
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
            //print_r ($responseData);
            return new DeleteVaResponseDto(
                $responseData['responseCode'],
                'Error deleting virtual account: ' . $responseData['responseMessage'] ?? $responseData['error'],
                null
            );
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
            //print_r ($responseData);
            return new CheckStatusVaResponseDto(
                $responseData['responseCode'],
                'Error checking status of virtual account: ' . $responseData['responseMessage'],
                null
            );
        }
    }

    public function convertVAInquiryRequestSnapToV1Form($snapJson): string
    {
        $snapData = json_decode($snapJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode JSON: " . json_last_error_msg());
        }
        $headers = $snapData['headers'] ?? [];
        $body = $snapData['body'] ?? [];
        $v1FormData = [
            'MALLID' => $headers['X-PARTNER-ID'] ?? '',
            'CHAINMERCHANT' => '', 
            'PAYMENTCHANNEL' => VaChannels::MAP_SNAP_TO_OCO_CHANNEL[$body['additionalInfo']['channel']] ?? '',
            'STATUSTYPE' => '',
            'WORDS' => '',  // in new v1, this field is not required, checksum will use X-SIGNATURE instead
            'OCOID' => $body['inquiryRequestId'] ?? ''
        ];

        return http_build_query($v1FormData);
    }

    public function convertVAInquiryResponseV1XmlToSnapJson($xmlString): string
    {
        $xml = simplexml_load_string($xmlString);
        if ($xml === false) {
            throw new Exception("Failed to parse XML");
        }

        $responseCodeToMessageMap = [
            "3000" => "Bill not found",
            "3001" => "Decline",
            "3002" => "Bill already paid",
            "3004" => "Account number / Bill was expired",
            "3006" => "VA Number not found",
            "0000" => "Success",
            "9999" => "Internal Error / Failed"
        ];

        $snapJson = [
            "responseCode" => (string)$xml->RESPONSECODE,
            "responseMessage" => $responseCodeToMessageMap[(string)$xml->RESPONSECODE] ?? '',
            "virtualAccountData" => [
                "partnerServiceId" => "", // Not provided in XML
                "customerNo" => (string)$xml->PAYMENTCODE, 
                "virtualAccountNo" => (string)$xml->PAYMENTCODE,
                "virtualAccountName" => (string)$xml->NAME,
                "virtualAccountEmail" => (string)$xml->EMAIL,
                "virtualAccountPhone" => "", // Not provided in XML
                "totalAmount" => [
                    "value" => number_format((float)$xml->AMOUNT, 2, '.', ''),
                    "currency" => "IDR"
                ],
                "virtualAccountTrxType" => "C",
                "expiredDate" => date('Y-m-d\TH:i:sP', strtotime((string)$xml->REQUESTDATETIME)),
                "additionalInfo" => [
                    "channel" => "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                    "trxId" => (string)$xml->TRANSIDMERCHANT,
                    "virtualAccountConfig" => [
                        "reusableStatus" => false,
                        "minAmount" => number_format((float)$xml->MINAMOUNT, 2, '.', ''),
                        "maxAmount" => number_format((float)$xml->MAXAMOUNT, 2, '.', '')
                    ]
                ],
                "inquiryStatus" => "", // Not provided in XML
                "inquiryReason" => [
                    "english" => "Success",
                    "indonesia" => "Sukses"
                ],
                "inquiryRequestId" => (string)$xml->SESSIONID,
                "freeText" => [
                    [
                        "english" => (string)$xml->ADDITIONALDATA,
                        "indonesia" => (string)$xml->ADDITIONALDATA
                    ]
                ]
            ]
        ];

        return json_encode($snapJson, JSON_PRETTY_PRINT);
    }

}