<?php
namespace Doku\Snap\Services;
use Doku\Snap\Commons\Helper;
use Doku\Snap\Commons\Config;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingResponseDto;
use Doku\Snap\Models\AccountBinding\AccountBindingAdditionalInfoResponseDto;
use Doku\Snap\Models\Payment\PaymentRequestDto;
use Doku\Snap\Models\Payment\PaymentResponseDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingResponseDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationResponseDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationAdditionalInfoResponseDto;
use Doku\Snap\Models\Refund\RefundRequestDto;
use Doku\Snap\Models\Refund\RefundResponseDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryRequestDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryResponseDto;
use Doku\Snap\Models\CheckStatus\CheckStatusRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusResponseDto;
use Doku\Snap\Models\CheckStatus\RefundHistoryDto;
use Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoResponseDto;
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitRequestDto;
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitResponseDto;
use Doku\Snap\Services\TokenServices;

class DirectDebitServices
{

    public function doPaymentJumpAppProcess(
        RequestHeaderDto $requestHeaderDto,
        PaymentJumpAppRequestDto $requestDto,
        string $isProduction
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
        string $isProduction
    ): AccountBindingResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
        $requestBody = $accountBindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
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
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_PAYMENT_URL;
        $requestBody = $paymentRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
       

        if (isset($responseObject['responseCode'])) {
            if ($responseObject['responseCode'] === '2005400') {
                return new PaymentResponseDto(
                    $responseObject['responseCode'],
                    $responseObject['responseMessage'],
                    $responseObject['webRedirectUrl'],
                    $responseObject['referenceNo']
                );
            }else{
                return  $responseObject;
            }
            
        }
    }

    public function doAccountUnbindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $isProduction
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
                $responseObject['referenceNo'],
                ""
            );
        } else {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                'Error unbinding account: ' . $responseObject['responseMessage'],
                '',
                ''
            );
        }
    }

    public function doCardUnbindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $isProduction
    ): AccountUnbindingResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_CARD_UNBINDING_URL;
        $requestBody = $accountUnbindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['referenceNo'],
                $responseObject['redirectUrl']
            );
        } else {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                'Error unbinding account: ' . $responseObject['responseMessage'],
                '',
                ''
            );
        }
    }

    public function doCardRegistrationProcess(
        RequestHeaderDto $requestHeaderDto,
        CardRegistrationRequestDto $requestDto,
        string $isProduction
    ): CardRegistrationResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CARD_REGISTRATION_URL;
        $requestBody = json_encode($requestDto);
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
            $additionalInfo = new CardRegistrationAdditionalInfoResponseDto(
                $responseObject['additionalInfo']['custIdMerchant'] ?? null,
                $responseObject['additionalInfo']['status'] ?? null,
                $responseObject['additionalInfo']['authCode'] ?? null
            );

            return new CardRegistrationResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['referenceNo'] ?? null,
                $responseObject['redirectUrl'] ?? null,
                $additionalInfo
            );
        } else {
            return new CardRegistrationResponseDto(
                $responseObject['responseCode'] ?? '5000500',
                'Error registering card: ' . ($responseObject['responseMessage'] ?? 'Unknown error'),
                null,
                null,
                null
            );
        }
    }

    public function doRefundProcess(
        RequestHeaderDto $header, 
        RefundRequestDto $refundRequestDto, 
        string $isProduction
    ): RefundResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_REFUND_URL;
        
        $headers = Helper::prepareHeaders($header);
        $requestBody = $refundRequestDto->generateJSONBody();
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        // Validate the response
        if (!isset($responseObject['responseCode']) || !isset($responseObject['responseMessage'])) {
            throw new \Exception("Invalid response from refund API");
        }

        // Create TotalAmount from response
        $refundAmount = new TotalAmount(
            $responseObject['refundAmount']['value'] ?? '',
            $responseObject['refundAmount']['currency'] ?? ''
        );

        return new RefundResponseDto(
            $responseObject['responseCode'],
            $responseObject['responseMessage'],
            $refundAmount,
            $responseObject['originalPartnerReferenceNo'] ?? '',
            $responseObject['originalReferenceNo'] ?? '',
            $responseObject['refundNo'] ?? '',
            $responseObject['partnerRefundNo'] ?? '',
            $responseObject['refundTime'] ?? ''
        );
    }

    public function doBalanceInquiryProcess(
        RequestHeaderDto $requestHeaderDto, 
        BalanceInquiryRequestDto $balanceInquiryRequestDto, 
        string $isProduction
    ): BalanceInquiryResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_BALANCE_INQUIRY_URL;
        $requestBody = $balanceInquiryRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '2000500') {
            return new BalanceInquiryResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['accountInfos']
            );
        } else {
            return new BalanceInquiryResponseDto(
                $responseObject['responseCode'] ?? '5000500',
                'Error performing balance inquiry: ' . ($responseObject['responseMessage'] ?? 'Unknown error'),
                []
            );
        }
    }

    public function doCheckStatus(
        RequestHeaderDto $requestHeaderDto,
        CheckStatusRequestDto $checkStatusRequestDto,
        string $isProduction
    ): CheckStatusResponseDto {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_CHECK_STATUS_URL;
        $requestBody = $checkStatusRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        if (isset($responseObject['responseCode']) && $responseObject['responseCode'] === '0000') {
            return new CheckStatusResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['originalPartnerReferenceNo'],
                $responseObject['originalReferenceNo'],
                $responseObject['approvalCode'],
                $responseObject['originalExternalId'],
                $responseObject['serviceCode'],
                $responseObject['latestTransactionStatus'],
                $responseObject['transactionStatusDesc'],
                $responseObject['originalResponseCode'],
                $responseObject['originalResponseMessage'],
                $responseObject['sessionId'],
                $responseObject['requestId'],
                $this->parseRefundHistory($responseObject['refundHistory']),
                new TotalAmount($responseObject['transAmount']['value'], $responseObject['transAmount']['currency']),
                new TotalAmount($responseObject['feeAmount']['value'], $responseObject['feeAmount']['currency']),
                $responseObject['paidTime'],
                new CheckStatusAdditionalInfoResponseDto(
                    $responseObject['additionalInfo']['deviceId'],
                    $responseObject['additionalInfo']['channel'],
                    $responseObject['additionalInfo']['acquirer'] ?? null
                )
            );
        } else {
            return new CheckStatusResponseDto(
                $responseObject['responseCode'],
                'Error checking direct debit status: ' . $responseObject['responseMessage'],
                $checkStatusRequestDto->originalPartnerReferenceNo,
                '', '', '', '', '', '', '', '', '', '',
                [],
                new TotalAmount('0', 'IDR'),
                new TotalAmount('0', 'IDR'),
                '',
                new CheckStatusAdditionalInfoResponseDto('', '')
            );
        }
    }

    private function parseRefundHistory(array $refundHistoryData): array
    {
        $refundHistory = [];
        foreach ($refundHistoryData as $refund) {
            $refundHistory[] = new RefundHistoryDto(
                $refund['refundNo'],
                $refund['partnerReferenceNo'],
                new TotalAmount($refund['refundAmount']['value'], $refund['refundAmount']['currency']),
                $refund['refundStatus'],
                $refund['refundDate'],
                $refund['reason']
            );
        }
        return $refundHistory;
    }

    public function handleDirectDebitNotification(
        NotifyPaymentDirectDebitRequestDto $requestDto,
        string $xSignature,
        string $xTimestamp,
        string $clientSecret,
        string $tokenB2B,
        string $isProduction
    ): NotifyPaymentDirectDebitResponseDto {
        // Validate the X-SIGNATURE
        $stringToSign = $this->createStringToSign($requestDto, $xTimestamp);
        $isValidSignature = $this->validateSymmetricSignature($xSignature, $stringToSign, $clientSecret. $tokenB2B);
        if (!$isValidSignature) {
            return new NotifyPaymentDirectDebitResponseDto(
                "4010000",
                Helper::generateExternalId(),
                "Unauthorized. Invalid Signature"
            );
        }

        return new NotifyPaymentDirectDebitResponseDto(
            '2005600',
            Helper::generateExternalId(),
            'Notification processed successfully'
        );
    }

    private function createStringToSign(NotifyPaymentDirectDebitRequestDto $requestDto, string $xTimestamp): string
    {
        $requestBody = json_encode($requestDto->generateJSONBody());
        return "POST:/v1.0/debit/notify:$xTimestamp:$requestBody";
    }

    private function validateSymmetricSignature(string $xSignature, string $stringToSign, string $clientSecret): bool
    {
        $tokenServices = new TokenServices();
        $generatedSignature = $tokenServices->generateSymmetricSignature(
            'POST',
            '/v1.0/debit/notify',
            '',
            $stringToSign,
            '',
            $clientSecret
        );
        $result = hash_equals($xSignature, $generatedSignature);
        return $result;
    }
}