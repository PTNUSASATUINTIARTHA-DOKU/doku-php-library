<?php

namespace Doku\Snap;

use Exception;

use Doku\Snap\Controllers\NotificationController;
use Doku\Snap\Controllers\TokenController;
use Doku\Snap\Controllers\VaController;
use Doku\Snap\Controllers\DirectDebitController;

use Doku\Snap\Models\Token\TokenB2BResponseDto;
use Doku\Snap\Models\Token\TokenB2B2CResponseDto;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\VA\Request\CreateVaRequestDtoV1;
use Doku\Snap\Models\VA\Response\CreateVaResponseDto;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Response\UpdateVaResponseDto;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\Notification\NotificationTokenDto;
use Doku\Snap\Models\Notification\PaymentNotificationRequestBodyDto;
use Doku\Snap\Models\Notification\PaymentNotificationResponseDto;
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Response\CheckStatusVaResponseDto;
use Doku\Snap\Models\VA\AdditionalInfo\Origin;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingResponseDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingResponseDto;
use Doku\Snap\Models\Payment\PaymentRequestDto;
use Doku\Snap\Models\Payment\PaymentResponseDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationResponseDto;
use Doku\Snap\Models\Refund\RefundRequestDto;
use Doku\Snap\Models\Refund\RefundResponseDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryRequestDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryResponseDto;
use Doku\Snap\Models\CheckStatus\CheckStatusResponseDto;
use Doku\Snap\Models\CheckStatus\CheckStatusRequestDto;

class Snap
{
    private VaController $vaController;
    private TokenController $tokenB2BController;
    private NotificationController $notificationController;
    private DirectDebitController $directDebitController;
    private string $privateKey;
    private string $clientId;
    private bool $isProduction;
    private string $tokenB2B;
    private int $tokenB2BExpiresIn = 900; // 15 minutes (900 seconds)
    private int $tokenB2BGeneratedTimestamp; 
    private string $tokenB2B2C;
    private int $tokenB2B2CExpiresIn = 900; // 15 minutes (900 seconds)
    private int $tokenB2B2CGeneratedTimestamp;
    private string $publicKey;
    private string $issuer;
    private ?string $secretKey;
    private ?string $deviceId = "";
    private ?string $ipAddress = "";

    public function __construct(string $privateKey, string $publicKey, string $clientId, string $issuer, bool $isProduction, string $secretKey, string $authCode)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->issuer = $issuer;
        $this->clientId =$clientId;
        $this->isProduction = $isProduction;
        $this->secretKey = $secretKey;

        $this->tokenB2BController = new TokenController();
        $this->notificationController = new NotificationController();
        $this->vaController = new VaController();
        $this->directDebitController = new DirectDebitController();

        $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
        $this->setTokenB2B($tokenB2BResponseDto);
        $tokenB2B2CResponseDto = $this->tokenB2BController->getTokenB2B2C($authCode, $privateKey, $clientId, $isProduction);
        $this->setTokenB2B2C($tokenB2B2CResponseDto);
    }

    private function validateString(string $input): string
    {
        $regex = '/[^A-Za-z0-9\-]/';
        return trim(preg_replace($regex, '', $input));
    }

    public function setTokenB2B(TokenB2BResponseDto $tokenB2BResponseDto)
    {
        $this->tokenB2B = $tokenB2BResponseDto->accessToken;
        $this->tokenB2BExpiresIn = $tokenB2BResponseDto->expiresIn - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenB2BGeneratedTimestamp = time();
    }


    /**
     * ONLY FOR TESTING
     */
    public function getTokenAndTime(): string
    {
        $env = '';
        if ($this->isProduction) {
            $env = 'Production';
        } else {
            $env = 'Sandbox';
        }
        $string = "Environment: " . $env . PHP_EOL;
        $string = $string . "TokenB2B: " . $this->tokenB2B . PHP_EOL;
        //$string = $string . "Generated timestamp: " . $this->tokenB2BGeneratedTimestamp . PHP_EOL;
        return $string  . "Expired In: " . $this->tokenB2BExpiresIn . PHP_EOL;
    }

    public function getB2BToken(string $privateKey, string $clientId, bool $isProduction): TokenB2BResponseDto
    {
        try {
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
            return $tokenB2BResponseDto;
        } catch (Exception $e) {
            return new TokenB2BResponseDto(
                "5007300",
                $e->getMessage(),
                "",
                "",
                0,
                ""
            );
        }
    }

    public function getCurrentTokenB2B(): string
    {
        return $this->tokenB2B;
    }

    public function getTokenB2B2C(string $authCode, string $privateKey, string $clientId, bool $isProduction): TokenB2B2CResponseDto
    {
        try {
            $tokenB2B2CResponseDto = $this->tokenB2BController->getTokenB2B2C($authCode, $privateKey, $clientId, $isProduction);
            $this->setTokenB2B2C($tokenB2B2CResponseDto);
            return $tokenB2B2CResponseDto;
        } catch (Exception $e) {
            return new TokenB2B2CResponseDto(
                "5007300",
                $e->getMessage(),
                "",
                "",
                "",
                "",
                "",
                null
            );
        }
    }

    public function setTokenB2B2C(TokenB2B2CResponseDto $tokenB2B2CResponseDto)
    {
        $this->tokenB2B2C = $tokenB2B2CResponseDto->accessToken;
        $this->tokenB2B2CExpiresIn = strtotime($tokenB2B2CResponseDto->accessTokenExpiryTime) - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenB2B2CGeneratedTimestamp = time();
    }

    public function createVa($createVaRequestDto): CreateVaResponseDto
    {
        $createVaRequestDto->validateCreateVaRequestDto();
        $createVaRequestDto->additionalInfo->origin = new Origin();
        $checkTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if($checkTokenInvalid){
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
        }	
        $createVaResponseDto = $this->vaController->createVa($createVaRequestDto, $this->privateKey, $this->clientId, $this->tokenB2B, $this->isProduction);
        return $createVaResponseDto;
    }

    public function generateNotificationResponse(bool $isTokenValid, ?PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
    {
        if ($isTokenValid) {
            if ($paymentNotificationRequestBodyDto !== null) {
                return $this->notificationController->generateNotificationResponse($paymentNotificationRequestBodyDto);
            } else {
                throw new Exception('If token is valid, please provide PaymentNotificationRequestBodyDto');
            }
        } else {
            return $this->notificationController->generateInvalidTokenResponse($paymentNotificationRequestBodyDto);
        }
    }

    public function validateSignature(string $requestSignature, string $requestTimestamp, string $privateKey, string $clientId): bool
    {
        return $this->tokenB2BController->validateSignature($requestSignature, $requestTimestamp, $privateKey, $clientId);
    }

    public function validateTokenAndGenerateNotificationResponse(RequestHeaderDto $requestHeaderDto, PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
    {
        $isTokenValid = $this->validateTokenB2B($requestHeaderDto->authorization);
        return $this->generateNotificationResponse($isTokenValid, $paymentNotificationRequestBodyDto);
    }

    public function validateTokenB2B(string $requestTokenB2B): bool
    {
        return $this->tokenB2BController->validateTokenB2B($requestTokenB2B, $this->publicKey);
    }

    public function validateSignatureAndGenerateToken(string $requestSignature, string $requestTimestamp): void
    {
        // Validate the signature
        $isSignatureValid = $this->validateSignature($requestSignature, $requestTimestamp, $this->privateKey, $this->clientId);

        // Generate a TokenB2B object based on the signature validity and set token
        $notificationTokenDto = $this->generateTokenB2BResponse($isSignatureValid);
        $notificationTokenBodyDto = $notificationTokenDto->body;
        $this->tokenB2B = $notificationTokenBodyDto->accessToken;
    }

    public function generateTokenB2BResponse(bool $isSignatureValid): NotificationTokenDto
    {
        if($isSignatureValid){
                return $this->tokenB2BController->generateTokenB2B($this->tokenB2BExpiresIn, $this->issuer, $this->privateKey, $this->clientId);
        }else{
                return $this->tokenB2BController->generateInvalidSignatureResponse();
        }
    }

    public function createVaV1(CreateVaRequestDtoV1 $createVaRequestDtoV1): CreateVaResponseDto
    {
        try {
            $createVaRequestDto = $createVaRequestDtoV1->convertToCreateVaRequestDto();
            $status = $createVaRequestDto->validateCreateVaRequestDto();
            return $this->createVa($createVaRequestDto);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function generateRequestHeader(string $channelId = "SDK"): RequestHeaderDto
    {
        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid(
            $this->tokenB2B,
            $this->tokenB2BExpiresIn,
            $this->tokenB2BGeneratedTimestamp
        );

        if ($isTokenInvalid) {
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B(
                $this->privateKey,
                $this->clientId,
                $this->isProduction
            );
            $this->setTokenB2B($tokenB2BResponseDto);
        }

        $requestHeaderDto = $this->tokenB2BController->doGenerateRequestHeader(
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $channelId
        );

        return $requestHeaderDto;
    }

    public function updateVa(UpdateVaRequestDto $updateVaRequestDto): UpdateVaResponseDto
    {
        if (!$updateVaRequestDto->validateUpdateVaRequestDto()) {
            return new UpdateVaResponseDto('400', 'Invalid request data', null);
        }

        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
        }

        $updateVaResponseDto = $this->vaController->doUpdateVa($updateVaRequestDto, $this->privateKey, $this->clientId, $this->tokenB2B, $this->secretKey, $this->isProduction);

        return $updateVaResponseDto;
    }

    public function deletePaymentCode(DeleteVaRequestDto $deleteVaRequestDto)
    {
        $deleteVaRequestDto->validateDeleteVaRequestDto();
        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid(
            $this->tokenB2B,
            $this->tokenB2BExpiresIn,
            $this->tokenB2BGeneratedTimestamp
        );

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B(
                $this->privateKey,
                $this->clientId,
                $this->isProduction
            );

            $this->setTokenB2B($tokenB2BResponse);
        }

        return $this->vaController->doDeletePaymentCode(
            $deleteVaRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->secretKey,
            $this->tokenB2B,
            $this->isProduction
        );
    }

    public function checkStatusVa(CheckStatusVaRequestDto $checkStatusVaRequestDto): CheckStatusVaResponseDto
    {
        $checkStatusVaRequestDto->validateCheckStatusVaRequestDto();
        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid(
            $this->tokenB2B,
            $this->tokenB2BExpiresIn,
            $this->tokenB2BGeneratedTimestamp
        );

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B(
                $this->privateKey,
                $this->clientId,
                $this->isProduction
            );
            $this->setTokenB2B($tokenB2BResponse);
        }

        $checkStatusVaResponseDto = $this->vaController->doCheckStatusVa(
            $checkStatusVaRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $this->isProduction
        );

        return $checkStatusVaResponseDto;
    }

    public function convertVAInquiryRequestSnapToV1Form($snapJson): string
    {
        return $this->vaController->convertVAInquiryRequestSnapToV1Form($snapJson);
    }

    public function convertVAInquiryResponseV1XmlToSnapJson($xmlString): string
    {
        return $this->vaController->convertVAInquiryResponseV1XmlToSnapJson($xmlString);
    }

    public function convertDOKUNotificationToForm($notification): string
    {
        return $this->notificationController->convertDOKUNotificationToForm($notification);
    }

    public function doPaymentJumpApp(
        PaymentJumpAppRequestDto $requestDto,
        string $deviceId,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): PaymentJumpAppResponseDto {
        $requestDto->validatePaymentJumpAppRequestDto();
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }
        
        $response = $this->directDebitController->doPaymentJumpApp($requestDto, $deviceId, $clientId, $this->tokenB2B, $secretKey, $isProduction);
        return $response;
    }


    public function doAccountBinding(
        AccountBindingRequestDto $accountBindingRequestDto,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): AccountBindingResponseDto {
        $accountBindingRequestDto->validateAccountBindingRequestDto();
        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }
        
        return $this->directDebitController->doAccountBinding(
            $accountBindingRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $this->deviceId,
            $this->ipAddress,
            $secretKey,
            $isProduction
        );
    }

    public function doPayment(
        PaymentRequestDto $paymentRequestDto,
        string $authCode,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): PaymentResponseDto {
        $paymentRequestDto->validatePaymentRequestDto();
        
        // Check token B2B
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        // Check token B2B2C
        $isTokenB2B2CInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B2C, $this->tokenB2B2CExpiresIn, $this->tokenB2B2CGeneratedTimestamp);
        if ($isTokenB2B2CInvalid) {
            $tokenB2B2CResponse = $this->tokenB2BController->getTokenB2B2C($authCode, $privateKey, $clientId, $isProduction);
            $this->setTokenB2B2C($tokenB2B2CResponse);
        }
        
        return $this->directDebitController->doPayment(
            $paymentRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $this->tokenB2B2C,
            $secretKey,
            $isProduction
        );
    }

    public function doAccountUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): AccountUnbindingResponseDto {
        $accountUnbindingRequestDto->validateAccountUnbindingRequestDto();

        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        return $this->directDebitController->doAccountUnbinding(
            $accountUnbindingRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $this->ipAddress,
            $secretKey,
            $isProduction
        );
    }

    public function doCardUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): AccountUnbindingResponseDto {
        $accountUnbindingRequestDto->validateAccountUnbindingRequestDto();

        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        return $this->directDebitController->doCardUnbinding(
            $accountUnbindingRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $this->ipAddress,
            $secretKey,
            $isProduction
        );
    }

    public function doCardRegistration(
        CardRegistrationRequestDto $cardRegistrationRequestDto,
        string $deviceId,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): CardRegistrationResponseDto {
        $cardRegistrationRequestDto->validate();
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }
        
        $response = $this->directDebitController->doCardRegistration($cardRegistrationRequestDto, $deviceId, $clientId, $this->tokenB2B, $secretKey, $isProduction);
        return $response;
    }

    public function doRefund(RefundRequestDto $refundRequestDto, $authCode, $privateKey, $clientId, $secretKey, $isProduction): RefundResponseDto
    {
        $refundRequestDto->validateRefundRequestDto();

        // Check token B2B
        $isTokenB2BInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2BInvalid) {
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
        }

        // Check token B2B2C
        $isTokenB2B2CInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B2C, $this->tokenB2B2CExpiresIn, $this->tokenB2B2CGeneratedTimestamp);
        if ($isTokenB2B2CInvalid) {
            $tokenB2B2CResponseDto = $this->tokenB2BController->getTokenB2B2C($authCode, $privateKey, $clientId, $isProduction);
            $this->setTokenB2B2C($tokenB2B2CResponseDto);
        }

        $refundResponseDto = $this->directDebitController->doRefund(
            $refundRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $this->tokenB2B2C,
            $secretKey,
            $isProduction
        );

        return $refundResponseDto;
    }

    public function doBalanceInquiry(BalanceInquiryRequestDto $balanceInquiryRequestDto, string $authCode): BalanceInquiryResponseDto
    {
        $balanceInquiryRequestDto->validateBalanceInquiryRequestDto();

        // Check token B2B
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        // Check token B2B2C
        $isTokenB2B2CInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B2C, $this->tokenB2B2CExpiresIn, $this->tokenB2B2CGeneratedTimestamp);
        if ($isTokenB2B2CInvalid) {
            $tokenB2B2CResponse = $this->tokenB2BController->getTokenB2B2C($authCode, $this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B2C($tokenB2B2CResponse);
        }

        return $this->directDebitController->doBalanceInquiry(
            $balanceInquiryRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->ipAddress,
            $this->tokenB2B2C,
            $this->tokenB2B,
            $this->secretKey,
            $this->isProduction
        );
    }

    public function doCheckStatus(
        CheckStatusRequestDto $checkStatusRequestDto,
        string $authCode,
        string $privateKey,
        string $clientId,
        string $secretKey,
        bool $isProduction
    ): CheckStatusResponseDto {
        $checkStatusRequestDto->validateCheckStatusRequestDto();

        // Check token B2B
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid(
            $this->tokenB2B,
            $this->tokenB2BExpiresIn,
            $this->tokenB2BGeneratedTimestamp
        );

        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        return $this->directDebitController->doCheckStatus(
            $checkStatusRequestDto,
            $privateKey,
            $clientId,
            $this->tokenB2B,
            $secretKey,
            $isProduction
        );
    }

    public function handleDirectDebitNotification(
        NotifyPaymentDirectDebitRequestDto $requestDto,
        string $xSignature,
        string $xTimestamp
    ): NotifyPaymentDirectDebitResponseDto {
        return $this->directDebitController->handleDirectDebitNotification(
            $requestDto,
            $xSignature,
            $xTimestamp,
            $this->secretKey
        );
    }
}