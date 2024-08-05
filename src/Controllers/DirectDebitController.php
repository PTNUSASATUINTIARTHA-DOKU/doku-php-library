<?php
namespace Doku\Snap\Controllers;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Services\DirectDebitServices;
use Doku\Snap\Services\TokenServices;
class DirectDebitController
{
    private TokenServices $tokenServices;
    private DirectDebitServices $directDebitServices;
    public function __construct()
    {
        $this->tokenServices = new TokenServices();
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
            $paymentJumpAppRequestDto->additionalInfo->channel,
            $clientId,
            $tokenB2B,
            $timestamp,
            $signature,
            $externalId
        );
        return $this->directDebitServices->doPaymentJumpAppProcess($header, $paymentJumpAppRequestDto, $isProduction);
    }
}