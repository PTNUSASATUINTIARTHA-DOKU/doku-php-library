<?php

namespace Doku\Snap;

use Error;
use Exception;
use InvalidArgumentException;

use Doku\Snap\Controllers\NotificationController;
use Doku\Snap\Controllers\TokenController;
use Doku\Snap\Controllers\VaController;

use Doku\Snap\Models\Token\TokenB2BResponseDto;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
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
use Doku\Snap\Models\Utilities\AdditionalInfo\Origin;


class Snap
{
    private VaController $vaController;
    private TokenController $tokenB2BController;
    private NotificationController $notificationController;
    private string $privateKey;
    private string $clientId;
    private bool $isProduction;
    private string $tokenB2B;
    private int $tokenB2BExpiresIn = 900; // 15 minutes (900 seconds)
    private int $tokenB2BGeneratedTimestamp; 
    private string $publicKey;
    private string $issuer;
    private ?string $secretKey;

    public function __construct(string $privateKey, string $publicKey, string $clientId, string $issuer, bool $isProduction, string $secretKey)
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

        $tokenB2BResponseDto = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
        $this->setTokenB2B($tokenB2BResponseDto);
    }

    private function validateString(string $input): string
    {
        // Perform string validation and sanitization here
        // TODO
        // Regex (waiting for the sanitation requirements)
        // prefix BRN / RCH (?), length still unknown
        // no empty string, must be char/digit (?)
        $regex = '/[^A-Za-z0-9\-]/';
        return trim(preg_replace($regex, '', $input));
    }

    public function setTokenB2B(TokenB2BResponseDto $tokenB2BResponseDto)
    {
        $this->tokenB2B = $tokenB2BResponseDto->accessToken;
        $this->tokenB2BExpiresIn = $tokenB2BResponseDto->expiresIn - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenB2BGeneratedTimestamp = time();

        // TODO
        // The code should be more efficient
        // kalau belum expire jangan lanjutin / tembak lagi 
        // persistent token should be handled
        // redis?
    }


    /**
     * ONLY FOR TESTING
     */
    public function getTokenAndTime(): string
    {
        $string = $this->tokenB2B . PHP_EOL;
        $string = $string . "Generated timestamp: " . $this->tokenB2BGeneratedTimestamp . PHP_EOL;
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

    public function createVa($createVaRequestDto): CreateVaResponseDto
    {
        $status = $createVaRequestDto->validateCreateVaRequestDto();
        $createVaRequestDto->additionalInfo->origin = new Origin();
        // TODO review is it referring to the same token or not
        // what if there are 2 merchant in same time hitting API
        // async or not
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
}