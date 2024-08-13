<?php
namespace Doku\Snap\Services;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingResponseDto;
use Doku\Snap\Models\Utilities\AdditionalInfo\AccountBindingAdditionalInfoResponseDto;
use Doku\Snap\Models\Payment\PaymentRequestDto;
use Doku\Snap\Models\Payment\PaymentResponseDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingResponseDto;

class DirectDebitServices
{
    public function doPaymentJumpAppProcess(
        RequestHeaderDto $requestHeaderDto,
        PaymentJumpAppRequestDto $requestDto,
        bool $isProduction
    ): PaymentJumpAppResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_PAYMENT_URL;
        $requestBody = $requestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
            return new PaymentJumpAppResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['webRedirectUrl'],
                $responseObject['partnerReferenceNo']
            );
        } else {
             return new PaymentJumpAppResponseDto(
                $responseObject['responseCode'],
                'Error creating virtual account: ' . $responseObject['responseMessage'],
                null,
                null
            );
        }
    }

    public function doAccountBindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountBindingRequestDto $accountBindingRequestDto,
        bool $isProduction
    ): AccountBindingResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
        $requestBody = $accountBindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode'])) {
            return new AccountBindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['referenceNo'],
                $responseObject['redirectUrl'],
                new AccountBindingAdditionalInfoResponseDto(
                    $responseObject['additionalInfo']['custIdMerchant'],
                    $responseObject['additionalInfo']['status'],
                    $responseObject['additionalInfo']['authCode']
                )
            );
        } else {
            return new AccountBindingResponseDto(
                $responseObject['responseCode'],
                'Error binding account: ' . $responseObject['responseMessage'],
                null,
                null,
                null
            );
        }
    }

    public function doPaymentProcess(
        RequestHeaderDto $requestHeaderDto,
        PaymentRequestDto $paymentRequestDto,
        bool $isProduction
    ): PaymentResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_PAYMENT_URL;
        $requestBody = $paymentRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode'])) {
            return new PaymentResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['webRedirectUrl'],
                $responseObject['partnerReferenceNo']
            );
        } else {
            return new PaymentResponseDto(
                $responseObject['responseCode'],
                'Error processing payment: ' . $responseObject['responseMessage'],
                '',
                $paymentRequestDto->partnerReferenceNo
            );
        }
    }

    public function doAccountUnbindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        bool $isProduction
    ): AccountUnbindingResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_UNBINDING_URL;
        $requestBody = $accountUnbindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['referenceNo']
            );
        } else {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                'Error unbinding account: ' . $responseObject['responseMessage'],
                ''
            );
        }
    }
}