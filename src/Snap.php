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

    public function getTokenB2B2C(string $authCode, string $privateKey, string $clientId, string $isProduction): TokenB2B2CResponseDto
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

        // Check if we're in sandbox mode and use simulation if so
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($createVaRequestDto->trxId, 'createVa');
            return new CreateVaResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                isset($simulatedResponse['virtualAccountData']) ? new CreateVaResponseVirtualAccountData(
                    $simulatedResponse['virtualAccountData']['partnerServiceId'],
                    $simulatedResponse['virtualAccountData']['customerNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountName'],
                    $simulatedResponse['virtualAccountData']['virtualAccountEmail'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    new TotalAmount(
                        $simulatedResponse['virtualAccountData']['totalAmount']['value'],
                        $simulatedResponse['virtualAccountData']['totalAmount']['currency']
                    ),
                    $simulatedResponse['virtualAccountData']['virtualAccountTrxType'],
                    $simulatedResponse['virtualAccountData']['expiredDate'],
                    new CreateVaResponseAdditionalInfo(
                        'VIRTUAL_ACCOUNT_BNC',
                        '',
                        ''
                    )
                ) : null
            );
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
        // Check if we're in sandbox mode and use simulation if so
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($updateVaRequestDto->trxId, 'updateVa');
            return new UpdateVaResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                isset($simulatedResponse['virtualAccountData']) ? new UpdateVaResponseVirtualAccountData(
                    $simulatedResponse['virtualAccountData']['partnerServiceId'],
                    $simulatedResponse['virtualAccountData']['customerNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountName'],
                    $simulatedResponse['virtualAccountData']['virtualAccountEmail'],
                    $simulatedResponse['virtualAccountData']['virtualAccountPhone'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    new TotalAmount(
                        $simulatedResponse['virtualAccountData']['totalAmount']['value'],
                        $simulatedResponse['virtualAccountData']['totalAmount']['currency']
                    ),
                    new \Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo(
                        'VIRTUAL_ACCOUNT_BNC',
                        new \Doku\Snap\Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig('ACTIVE')
                    ),
                    $simulatedResponse['virtualAccountData']['virtualAccountTrxType'],
                    $simulatedResponse['virtualAccountData']['expiredDate']
                ) : null
            );
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

        // Check if we're in sandbox mode and use simulation if so
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($deleteVaRequestDto->trxId, 'deleteVa');
            return new \Doku\Snap\Models\VA\Response\DeleteVaResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                isset($simulatedResponse['virtualAccountData']) ? new DeleteVaResponseVirtualAccountData(
                    $simulatedResponse['virtualAccountData']['partnerServiceId'],
                    $simulatedResponse['virtualAccountData']['customerNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountNo'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    new \Doku\Snap\Models\VA\AdditionalInfo\DeleteVaResponseAdditionalInfo(
                        $simulatedResponse['virtualAccountData']['additionalInfo']['channel'],
                        ''
                    )
                ) : null
            );
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

    public function checkStatusVa(CheckStatusVaRequestDto $checkStatusVaRequestDto): CheckStatusVaResponseDto
    {
        $checkStatusVaRequestDto->validateCheckStatusVaRequestDto();

        // Check if we're in sandbox mode and use simulation if so
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($checkStatusVaRequestDto->virtualAccountNo ?? '1113', 'checkStatusVa');
            return new CheckStatusVaResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                isset($simulatedResponse['virtualAccountData']) ? new CheckStatusVirtualAccountData(
                    new CheckStatusResponsePaymentFlagReason('Success', 'Sukses'),
                    $simulatedResponse['virtualAccountData']['partnerServiceId'],
                    $simulatedResponse['virtualAccountData']['customerNo'],
                    $simulatedResponse['virtualAccountData']['virtualAccountNo'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    $simulatedResponse['virtualAccountData']['trxId'],
                    new TotalAmount(
                        $simulatedResponse['virtualAccountData']['totalAmount']['value'],
                        $simulatedResponse['virtualAccountData']['totalAmount']['currency']
                    ),
                    new TotalAmount(
                        $simulatedResponse['virtualAccountData']['totalAmount']['value'],
                        $simulatedResponse['virtualAccountData']['totalAmount']['currency']
                    ),
                    new CheckStatusVaResponseAdditionalInfo('VIRTUAL_ACCOUNT_BNC')
                ) : null
            );
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
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($requestDto->partnerReferenceNo, 'paymentJumpApp');
            return new PaymentJumpAppResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                $simulatedResponse['responseCode'] === '2000500' ? 'https://example.com/redirect' : null,
                $requestDto->partnerReferenceNo
            );
        }

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
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($accountBindingRequestDto->phoneNo, 'accountBinding');
            return new AccountBindingResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                $simulatedResponse['responseCode'] === '2000500' ? 'REF123' : null,
                $simulatedResponse['responseCode'] === '2000500' ? 'https://example.com/redirect' : null,
                new \Doku\Snap\Models\AccountBinding\AccountBindingAdditionalInfoResponseDto(
                    'CUST123',
                    'SUCCESS',
                    'AUTH123'
                )
            );
        }
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
        CardRegistrationRequestDto $cardRegistrationRequestDto,
        string $deviceId,
        string $privateKey,
        string $clientId,
        string $secretKey,
        string $isProduction
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
        string $isProduction
    ): CheckStatusResponseDto {
        $checkStatusRequestDto->validateCheckStatusRequestDto();

        // Check if we're in sandbox mode and use simulation if so
        if (!$this->isProduction && $this->isSimulation) {
            $simulatedResponse = $this->simulateTransferVA($checkStatusRequestDto->originalPartnerReferenceNo, 'checkStatus');
            return new CheckStatusResponseDto(
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                $checkStatusRequestDto->originalPartnerReferenceNo,
                'REF123',
                'APPROVAL123',
                $checkStatusRequestDto->originalExternalId,
                $checkStatusRequestDto->serviceCode,
                'SUCCESS',
                'Transaction successful',
                $simulatedResponse['responseCode'],
                $simulatedResponse['responseMessage'],
                'SESSION123',
                'REQUEST123',
                [],
                $checkStatusRequestDto->amount,
                new TotalAmount('1000.00', 'IDR'),
                '2023-01-01T12:00:00+07:00',
                new \Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoResponseDto(
                    'DEVICE123',
                    'CHANNEL123'
                )
            );
        }

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

    public function simulateTransferVA(string $trxId, string $action): array
    {
        // Only run simulations in sandbox environment
        if ($this->isProduction) {
            throw new Exception("Simulations can only be run in sandbox environment");
        }

        $scenarios = [
            '111' => [
                'responseCode' => '401xx01',
                'responseMessage' => 'Access Token Invalid'
            ],
            '112' => [
                'responseCode' => '401xx00',
                'responseMessage' => 'Unauthorized . Signature Not Match'
            ],
            '113' => [
                'responseCode' => '400xx02',
                'responseMessage' => 'Missing Mandatory Field {partnerServiceId}'
            ],
            '114' => [
                'responseCode' => '400xx01',
                'responseMessage' => 'Invalid Field Format {totalAmount.currency}'
            ],
            '115' => [
                'responseCode' => '409xx00',
                'responseMessage' => 'Conflict'
            ],
            '116' => [
                'responseCode' => '2002400',
                'responseMessage' => 'success'
            ],
            '117' => [
                'responseCode' => '4042414',
                'responseMessage' => 'Bill has been paid'
            ],
            '118' => [
                'responseCode' => '4042419',
                'responseMessage' => 'Bill expired'
            ],
            '119' => [
                'responseCode' => '4042412',
                'responseMessage' => 'Bill not found'
            ],
            '1110' => [
                'responseCode' => '2002500',
                'responseMessage' => 'success'
            ],
            '1111' => [
                'responseCode' => '4042512',
                'responseMessage' => 'Bill not found'
            ],
            '1112' => [
                'responseCode' => '4042513',
                'responseMessage' => 'Invalid Amount'
            ],
            '1113' => [
                'responseCode' => '2002600',
                'responseMessage' => 'success'
            ],
            '1114' => [
                'responseCode' => '2002700',
                'responseMessage' => 'success'
            ],
            '1115' => [
                'responseCode' => '2002800',
                'responseMessage' => 'success'
            ],
            '1116' => [
                'responseCode' => '2002900',
                'responseMessage' => 'success'
            ],
            '1117' => [
                'responseCode' => '2003000',
                'responseMessage' => 'success'
            ],
            '1118' => [
                'responseCode' => '2003100',
                'responseMessage' => 'success'
            ],
        ];

        if (!isset($scenarios[$trxId])) {
            throw new Exception("Unknown simulation scenario");
        }

        $response = $scenarios[$trxId];

        // Add additional data based on the action
        switch ($action) {
            case 'createVa':
            case 'updateVa':
            case 'checkStatusVa':
                if (in_array($response['responseCode'], ['2002700', '2002800', '2002600'])) {
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
        }

        return $response;
    }

}