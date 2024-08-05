<?php
namespace Doku\Snap\Services;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;


class DirectDebitServices
{
    public function doPaymentJumpAppProcess(
        RequestHeaderDto $requestHeaderDto,
        PaymentJumpAppRequestDto $requestDto,
        bool $isProduction
    ): PaymentJumpAppResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CREATE_VA;
        $requestBody = $requestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        //TODO return response
        return new PaymentJumpAppResponseDto();
    }
}