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
        string $privateKey,
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
            $tokenB2B
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
            $ipAddress
        );

        return $this->directDebitServices->doAccountUnbindingProcess($header, $accountUnbindingRequestDto, $isProduction);
    }
}