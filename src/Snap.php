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
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\VirtualAccountData\CreateVaResponseVirtualAccountData;
use Doku\Snap\Models\VA\AdditionalInfo\CreateVaResponseAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountData\UpdateVaResponseVirtualAccountData;
use Doku\Snap\Models\VA\VirtualAccountData\DeleteVaResponseVirtualAccountData;
use Doku\Snap\Models\VA\VirtualAccountData\CheckStatusVirtualAccountData;
use Doku\Snap\Models\VA\AdditionalInfo\CheckStatusVaResponseAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountData\CheckStatusResponsePaymentFlagReason;
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitRequestDto;
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitResponseDto;

class Snap
{
    private VaController $vaController;
    private TokenController $tokenB2BController;
    private NotificationController $notificationController;
    private DirectDebitController $directDebitController;
    private string $privateKey;
    private string $clientId;
    private string $isProduction;
    private string $tokenB2B;
    private int $tokenB2BExpiresIn = 900; // 15 minutes (900 seconds)
    private int $tokenB2BGeneratedTimestamp; 
    private ?string $tokenB2B2C = "";
    private int $tokenB2B2CExpiresIn = 900; // 15 minutes (900 seconds)
    private ?int $tokenB2B2CGeneratedTimestamp = 0;
    private string $publicKey;
    private string $issuer;
    private ?string $secretKey;
    private ?string $deviceId = "";
    private ?string $ipAddress = "";
    private bool $isSimulation = false;
  
    public function __construct(string $privateKey, string $publicKey, string $dokuPublicKey, string $clientId, string $issuer, string $isProduction, string $secretKey)

    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->dokuPublicKey = $dokuPublicKey;
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

    public function getB2BToken(): TokenB2BResponseDto
    {
        try {
            $result = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($result);
            return $result;
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

    public function getTokenB2B2C(string $authCode): TokenB2B2CResponseDto
    {
        try {
            $tokenB2B2CResponseDto = $this->tokenB2BController->getTokenB2B2C($authCode, $this->privateKey, $this->clientId, $this->isProduction);
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

    public function createVa($createVaRequestDto)
    {
        $createVaRequestDto->validateCreateVaRequestDto();
        $createVaRequestDto->additionalInfo->origin = new Origin();

        $simulatedResponse = $this->simulateTransferVA($createVaRequestDto->trxId,$createVaRequestDto->virtualAccountNo, 'createVa');
        if (is_array($simulatedResponse) && isset($simulatedResponse['responseCode']) && $simulatedResponse['responseCode'] !== '0010') {
            return $simulatedResponse;
        }
      
        $checkTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if($checkTokenInvalid){
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
        }	
        $createVaResponseDto = $this->vaController->createVa($createVaRequestDto, $this->privateKey, $this->clientId, $this->tokenB2B,$this->secretKey, $this->isProduction);
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

    public function validateSignature(string $requestSignature, string $requestTimestamp, string $clientId): bool
    {
        return $this->tokenB2BController->validateSignature($requestSignature, $requestTimestamp, $clientId, $this->dokuPublicKey);
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

    public function validateSignatureAndGenerateToken(string $requestSignature, string $requestTimestamp)
    {
        // Validate the signature
        $isSignatureValid = $this->validateSignature($requestSignature, $requestTimestamp, $this->clientId);
        // Generate a TokenB2B object based on the signature validity and set token
        return $this->generateTokenB2BResponse($isSignatureValid);
      
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

    public function updateVa(UpdateVaRequestDto $updateVaRequestDto)
    {
        if (!$updateVaRequestDto->validateUpdateVaRequestDto()) {
            return new UpdateVaResponseDto('400', 'Invalid request data', null);
        }
        $simulatedResponse = $this->simulateTransferVA($updateVaRequestDto->trxId,$updateVaRequestDto->virtualAccountNo, 'updateVa');
        if (is_array($simulatedResponse) && isset($simulatedResponse['responseCode']) && $simulatedResponse['responseCode'] !== '0010') {
            return $simulatedResponse;
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

        $simulatedResponse = $this->simulateTransferVA($deleteVaRequestDto->trxId,$deleteVaRequestDto->virtualAccountNo, 'deleteVa');
        if (is_array($simulatedResponse) && isset($simulatedResponse['responseCode']) && $simulatedResponse['responseCode'] !== '0010') {
            return $simulatedResponse;
        }

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

    public function checkStatusVa(CheckStatusVaRequestDto $checkStatusVaRequestDto)
    {
        $checkStatusVaRequestDto->validateCheckStatusVaRequestDto();

        $simulatedResponse = $this->simulateTransferVA("",$checkStatusVaRequestDto->virtualAccountNo, 'checkStatusVa');
        if (is_array($simulatedResponse) && isset($simulatedResponse['responseCode']) && $simulatedResponse['responseCode'] !== '0010') {
            return $simulatedResponse;
        }

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
        string $isProduction
    ): PaymentJumpAppResponseDto {
        $requestDto->validatePaymentJumpAppRequestDto();

        // Check if we're in sandbox mode and use simulation if so
      

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
        string $ipAddress,
        string $deviceId
    ): AccountBindingResponseDto {
        $accountBindingRequestDto->validateAccountBindingRequestDto();
        // Check if we're in sandbox mode and use simulation if so
      
        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }
        
        return $this->directDebitController->doAccountBinding(
            $accountBindingRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $deviceId,
            $ipAddress,
            $this->secretKey,
            $this->isProduction
        );
    }

    public function doPayment(
        PaymentRequestDto $paymentRequestDto,
        string $authCode,
        string $ipAddress
    ) {
        $paymentRequestDto->validatePaymentRequestDto();
        
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
        return $this->directDebitController->doPayment(
            $paymentRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $this->tokenB2B2C,
            $this->secretKey,
            $ipAddress,
            $this->isProduction
        );
    }

    public function doAccountUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $ipAddress
    ): AccountUnbindingResponseDto {
        $accountUnbindingRequestDto->validateAccountUnbindingRequestDto();

        $isTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);

        if ($isTokenInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }

        return $this->directDebitController->doAccountUnbinding(
            $accountUnbindingRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $ipAddress,
            $this->secretKey,
            $this->isProduction
        );
    }

    public function doCardUnbinding(
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $privateKey,
        string $clientId,
        string $secretKey,
        string $isProduction
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
        CardRegistrationRequestDto $cardRegistrationRequestDto
    ): CardRegistrationResponseDto {
        $cardRegistrationRequestDto->validate();
        $isTokenB2bInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2bInvalid) {
            $tokenB2BResponse = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponse);
        }
        
        $response = $this->directDebitController->doCardRegistration($cardRegistrationRequestDto, $this->clientId, $this->tokenB2B, $this->secretKey, $this->isProduction);
        return $response;
    }

    public function doRefund(RefundRequestDto $refundRequestDto, $authCode, $ipAddress,$deviceId): RefundResponseDto
    {
        $refundRequestDto->validateRefundRequestDto();

        // Check token B2B
        $isTokenB2BInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if ($isTokenB2BInvalid) {
            $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponseDto);
        }

        // Check token B2B2C
        $isTokenB2B2CInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B2C, $this->tokenB2B2CExpiresIn, $this->tokenB2B2CGeneratedTimestamp);
        if ($isTokenB2B2CInvalid) {
            $tokenB2B2CResponseDto = $this->tokenB2BController->getTokenB2B2C($authCode, $this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B2C($tokenB2B2CResponseDto);
        }

        $refundResponseDto = $this->directDebitController->doRefund(
            $refundRequestDto,
            $this->privateKey,
            $this->clientId,
            $this->tokenB2B,
            $this->tokenB2B2C,
            $this->secretKey,
            $ipAddress,
            $deviceId,
            $this->isProduction
        );

        return $refundResponseDto;
    }

    public function doBalanceInquiry(BalanceInquiryRequestDto $balanceInquiryRequestDto, string $authCode, string $ipAddress): BalanceInquiryResponseDto
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
            $ipAddress,
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
        string $isProduction
    ): CheckStatusResponseDto {
        $checkStatusRequestDto->validateCheckStatusRequestDto();

        // Check if we're in sandbox mode and use simulation if so
      

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
        string $xTimestamp,
        string $secretKey,
        string $tokenB2B,
        string $isProduction
    ): NotifyPaymentDirectDebitResponseDto {
        return $this->directDebitController->handleDirectDebitNotification(
            $requestDto,
            $xSignature,
            $xTimestamp,
            $secretKey,
            $tokenB2B,
            $isProduction
        );
    }

    public function simulateTransferVA(string $trxId, string $virtualAccountNo, string $action)
    {
        $scenarios = [
            '111' => [
                'createVa' => ['responseCode' => '4012701', 'responseMessage' => 'Access Token Invalid']
            ],
            '112' => [
                'createVa' => ['responseCode' => '4012700', 'responseMessage' => 'Unauthorized. Signature Not Match']
            ],
            '113' => [
                'createVa' => ['responseCode' => '4002702', 'responseMessage' => 'Invalid Mandatory Field {partnerServiceId}']
            ],
            '114' => [
                'createVa' => ['responseCode' => '4002702', 'responseMessage' => 'Invalid Field Format {totalAmount.currency}']
            ],
            '115' => [
                'createVa' => ['responseCode' => '4092700', 'responseMessage' => 'Conflict']
            ],
            '116' => [
                'inquiryVa' => ['responseCode' => '2002400', 'responseMessage' => 'success']
            ],
            '117' => [
                'inquiryVa' => ['responseCode' => '4042414', 'responseMessage' => 'Bill has been paid']
            ],
            '118' => [
                'inquiryVa' => ['responseCode' => '4042419', 'responseMessage' => 'Bill expired']
            ],
            '119' => [
                'inquiryVa' => ['responseCode' => '4042412', 'responseMessage' => 'Bill not found']
            ],
            '1110' => [
                'paymentVa' => ['responseCode' => '2002500', 'responseMessage' => 'success']
            ],
            '1111' => [
                'paymentVa' => ['responseCode' => '4042512', 'responseMessage' => 'Bill not found']
            ],
            '1112' => [
                'paymentVa' => ['responseCode' => '4042513', 'responseMessage' => 'Invalid Amount']
            ],
            '1113' => [
                'checkStatusVa' => ['responseCode' => '2002600', 'responseMessage' => 'success']
            ],
            '1114' => [
                'createVa' => ['responseCode' => '2002700', 'responseMessage' => 'success']
            ],
            '1115' => [
                'createVa' => ['responseCode' => '2002800', 'responseMessage' => 'success']
            ],
            '1116' => [
                'updateVa' => ['responseCode' => '2002900', 'responseMessage' => 'success']
            ],
            '1117' => [
                'inquiryVa' => ['responseCode' => '2003000', 'responseMessage' => 'success']
            ],
            '1118' => [
                'deleteVa' => ['responseCode' => '2003100', 'responseMessage' => 'success']
            ]
        ];
        
        $f3DgtTrxId = substr(strval($trxId), 0, 3);
        $f4DgtTrxId = substr(strval($trxId), 0, 4);
        $f3DgtVaNo = substr(strval($virtualAccountNo), 0, 3);
        $f4DgtVaNo = substr(strval($virtualAccountNo), 0, 4);
        
        // Mengakses skenario berdasarkan trxId atau virtualAccountNo dan action
        if (isset($scenarios[$f4DgtTrxId][$action])) {
            $response = $scenarios[$f4DgtTrxId][$action];
        } elseif (isset($scenarios[$f3DgtTrxId][$action])) {
            $response = $scenarios[$f3DgtTrxId][$action];
        } elseif (isset($scenarios[$f4DgtVaNo][$action])) {
            $response = $scenarios[$f4DgtVaNo][$action];
        } elseif (isset($scenarios[$f3DgtVaNo][$action])) {
            $response = $scenarios[$f3DgtVaNo][$action];
        } else {
            $response = ['responseCode' => '0010', 'responseMessage' => 'unknown'];
        }
        
        switch ($action) {
            case 'createVa':
            case 'updateVa':
            case 'checkStatusVa':
                if (in_array($response['responseCode'], ['2002700'])) {
                    $response['virtualAccountData'] = [
                        'partnerServiceId' => '90341589',
                        'customerNo' => '00000077',
                        'virtualAccountNo' => '9034153700000077',
                        'virtualAccountName' => 'Jokul Doe 001',
                        'virtualAccountEmail' => 'jokul@email.com',
                        'virtualAccountPhone' => '',
                        'trxId' => $trxId,
                        'totalAmount' => [
                            'value' => '13000.00',
                            'currency' => 'IDR'
                        ],
                        'virtualAccountTrxType' => 'C',
                        'expiredDate' => '2024-02-02T15:02:29+07:00'
                    ];
                }
                break;
            case 'deleteVa':
                if ($response['responseCode'] === '2003100') {
                    $response['virtualAccountData'] = [
                        'partnerServiceId' => '90341589',
                        'customerNo' => '00000077',
                        'virtualAccountNo' => '9034153700000077',
                        'trxId' => $trxId,
                        'additionalInfo' => [
                            'channel' => 'VIRTUAL_ACCOUNT_BNC'
                        ]
                    ];
                }
                break;
            default:
                // Handle unknown action
                $response = [
                    'responseCode' => '500xx00',
                    'responseMessage' => 'Unknown action: ' . $action
                ];
                break;
        }
    
        return $response;
    }
    

}