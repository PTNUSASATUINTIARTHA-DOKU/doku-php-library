<?php
namespace Doku\Snap\Controllers;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Services\DirectDebitServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
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
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitRequestDto;
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitResponseDto;
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
        string $isProduction
    ): PaymentJumpAppResponseDto {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_PAYMENT_URL;
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
        string $isProduction
    ): AccountBindingResponseDto {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
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
        string $ipAddress,
        string $isProduction
        
    ) {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_PAYMENT_URL;
        $httpMethod = 'POST';
        $signature = $this->tokenServices->generateSymmetricSignature(
            $httpMethod, 
            $apiEndpoint, 
            $tokenB2B, 
            $paymentRequestDto->generateJSONBody(), 
            $timestamp, 
            $secretKey
        );
        $externalId = time();
        $header = Helper::generateRequestHeaderDto(
            $timestamp, 
            $signature, 
            $clientId, 
            $externalId, 
            null, 
            $tokenB2B, 
            $ipAddress, 
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
        string $isProduction
    ): AccountUnbindingResponseDto {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_ACCOUNT_UNBINDING_URL;
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

    public function doCardUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $privateKey,
        string $clientId,
        string $tokenB2B,
        string $ipAddress,
        string $secretKey,
        string $isProduction
    ): AccountUnbindingResponseDto {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_ACCOUNT_UNBINDING_URL;
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

        return $this->directDebitServices->doCardUnbindingProcess($header, $accountUnbindingRequestDto, $isProduction);
    }

    public function doCardRegistration(
        CardRegistrationRequestDto $cardRegistrationRequestDto,
        string $deviceId,
        string $clientId,
        string $tokenB2B,
        string $secretKey,
        string $isProduction
    ): CardRegistrationResponseDto {
        $cardData = json_encode($cardRegistrationRequestDto->cardData);
        $encryptCbc = $this->directDebitServices->encryptCbc($cardData,$secretKey);
        $cardRegistrationRequestDto->cardData = $encryptCbc;
        $timestamp = Helper::getTimestamp();
        $apiEndpoint = Config::CARD_REGISTRATION_URL;
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
        string $isProduction
    ): BalanceInquiryResponseDto {
        $timestamp = $this->tokenServices->getTimestamp();
        $apiEndpoint = Config::DIRECT_DEBIT_BALANCE_INQUIRY_URL;
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
        string $isProduction
    ): CheckStatusResponseDto {
        $timestamp = Helper::getTimestamp();
        $apiEndpoint =Config::DIRECT_DEBIT_CHECK_STATUS_URL;

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

    public function handleDirectDebitNotification(
        NotifyPaymentDirectDebitRequestDto $requestDto,
        string $xSignature,
        string $xTimestamp,
        string $clientSecret,
        string $tokenB2B,
        string $isProduction
    ): NotifyPaymentDirectDebitResponseDto {
        return $this->directDebitServices->handleDirectDebitNotification(
            $requestDto,
            $xSignature,
            $xTimestamp,
            $clientSecret,
            $tokenB2B,
            $isProduction
        );
    }
}