<?php
namespace Doku\Snap\Controllers;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Services\DirectDebitServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingResponseDto;
use Doku\Snap\Models\Payment\PaymentRequestDto;
use Doku\Snap\Models\Payment\PaymentResponseDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingResponseDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationResponseDto;
use Doku\Snap\Models\Refund\RefundRequestDto;
use Doku\Snap\Models\Refund\RefundResponseDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryRequestDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryResponseDto;
use Doku\Snap\Models\CheckStatus\CheckStatusRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusResponseDto;
class DirectDebitController
{
    private TokenServices $tokenServices;
    private DirectDebitServices $directDebitServices;
    public function __construct()
    {
        $this->tokenServices = new TokenServices();
        $this->directDebitServices = new DirectDebitServices();
    }
    public function doPaymentJumpApp(
        PaymentJumpAppRequestDto $paymentJumpAppRequestDto,
        string $deviceId,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        bool $isProduction
    ): PaymentJumpAppResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_PAYMENT_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $paymentJumpAppRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $externalId = Helper::generateExternalId();;
        $header = Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $paymentJumpAppRequestDto->additionalInfo->channel,
            $tokenB2B,
            null,
            $deviceId,
            null
        );
        return $this->directDebitServices->doPaymentJumpAppProcess($header, $paymentJumpAppRequestDto, $isProduction);
    }
    public function doAccountBinding(
        AccountBindingRequestDto $accountBindingRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $deviceId,
        string $ipAddress,
        string $secretKey,
        bool $isProduction
    ): AccountBindingResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod, 
            $apiEndpoint, 
            $tokenB2B, 
            $accountBindingRequestDto->generateJSONBody(), 
            $timestamp, 
            $secretKey
        );
        $externalId = Helper::generateExternalId();
        $header = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature, 
            $clientId, 
            $externalId, 
            null,
            $tokenB2B,
            $ipAddress,
            $deviceId,
            null
        );

        return $this->directDebitServices->doAccountBindingProcess($header, $accountBindingRequestDto, $isProduction);
    }

    public function doPayment(
        PaymentRequestDto $paymentRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $tokenB2b2c,
        string $secretKey,
        bool $isProduction
    ): PaymentResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod, 
            $apiEndpoint, 
            $tokenB2B, 
            $paymentRequestDto->generateJSONBody(), 
            $timestamp, 
            $secretKey
        );
        $externalId = Helper::generateExternalId();
        $header = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature, 
            $clientId, 
            $externalId, 
            null, 
            $tokenB2B, 
            null, 
            null, 
            $tokenB2b2c
        );

        return $this->directDebitServices->doPaymentProcess($header, $paymentRequestDto, $isProduction);
    }

    public function doAccountUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $ipAddress,
        string $secretKey,
        bool $isProduction
    ): AccountUnbindingResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_UNBINDING_URL;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod, 
            $apiEndpoint, 
            $tokenB2B, 
            $accountUnbindingRequestDto->generateJSONBody(), 
            $timestamp, 
            $secretKey);
        $externalId =  Helper::generateExternalId();
        $header =  Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature, 
            $clientId, 
            $externalId, 
            null, 
            $tokenB2B, 
            $ipAddress,
            null,
            null
        );

        return $this->directDebitServices->doAccountUnbindingProcess($header, $accountUnbindingRequestDto, $isProduction);
    }

    public function doCardRegistration(
        CardRegistrationRequestDto $cardRegistrationRequestDto,
        string $deviceId,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        bool $isProduction
    ): CardRegistrationResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CARD_REGISTRATION_URL;
        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $cardRegistrationRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $externalId = Helper::generateExternalId();
        $header = Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $cardRegistrationRequestDto->additionalInfo->channel,
            $tokenB2B,
            null,
            $deviceId,
            null
        );
        return $this->directDebitServices->doCardRegistrationProcess($header, $cardRegistrationRequestDto, $isProduction);
    }

    public function doRefund(RefundRequestDto $refundRequestDto, $privateKey, $clientId, $tokenB2B, $tokenB2B2C, $secretKey, $isProduction): RefundResponseDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        $endPointUrl = Config::getBaseURL($isProduction) . Config::DIRECT_DEBIT_REFUND_URL;
        $httpMethod = 'POST';

        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod,
            $endPointUrl,
            $tokenB2B,
            $refundRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );

        $externalId = Helper::generateExternalId();
        
        $header = Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            null, // channelId
            $tokenB2B,
            null, // ipAddress
            null, // deviceId
            $tokenB2B2C
        );

        return $this->directDebitServices->doRefundProcess($header, $refundRequestDto, $isProduction);
    }

    public function doBalanceInquiry(
        BalanceInquiryRequestDto $balanceInquiryRequestDto,
        string $privateKey,
        string $clientId,
        string $ipAddress,
        string $tokenB2b2c,
        string $tokenB2B,
        string $secretKey,
        bool $isProduction
    ): BalanceInquiryResponseDto {
        $timestamp = $this->tokenServices->getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_BALANCE_INQUIRY_URL;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod,
            $apiEndpoint,
            $tokenB2B,
            $balanceInquiryRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );
        $externalId = Helper::generateExternalId();
        $header = Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $balanceInquiryRequestDto->additionalInfo->channel,
            $tokenB2B,
            $ipAddress,
            null,
            $tokenB2b2c
        );

        return $this->directDebitServices->doBalanceInquiryProcess($header, $balanceInquiryRequestDto, $isProduction);
    }


    public function doCheckStatus(
        CheckStatusRequestDto $checkStatusRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        bool $isProduction
    ): CheckStatusResponseDto {
        $timestamp = Helper::getTimestamp();
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_CHECK_STATUS_URL;

        $signature = $this->tokenServices->generateSymmetricSignature(
            'POST',
            $apiEndpoint,
            $tokenB2B,
            $checkStatusRequestDto->generateJSONBody(),
            $timestamp,
            $secretKey
        );

        $externalId = Helper::generateExternalId();
        $header = Helper::generateRequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            null,
            $tokenB2B,
            null,
            null,
            null
        );

        return $this->directDebitServices->doCheckStatus($header, $checkStatusRequestDto, $isProduction);
    }
}