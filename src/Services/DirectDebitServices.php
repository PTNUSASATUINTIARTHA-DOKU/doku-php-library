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
use Doku\Snap\Models\CheckStatus\DirectDebitCheckStatusRequestDto;
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
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_PAYMENT_URL;
        $requestBody = $requestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return new PaymentJumpAppResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['webRedirectUrl'],
                isset($responseObject['partnerReferenceNo']) ? $responseObject['partnerReferenceNo'] : null,
                isset($responseObject['additionalInfo']) ? $responseObject['additionalInfo'] : null
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }

    public function doAccountBindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountBindingRequestDto $accountBindingRequestDto,
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_BINDING_URL;
        $requestBody = $accountBindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return new AccountBindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                isset($responseObject['referenceNo']) ? $responseObject['referenceNo'] : null,
                isset($responseObject['redirectUrl']) ? $responseObject['redirectUrl'] : null,
                new AccountBindingAdditionalInfoResponseDto(
                    $responseObject['additionalInfo']['custIdMerchant'],
                    $responseObject['additionalInfo']['accountStatus'],
                    $responseObject['additionalInfo']['authCode']
                )
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
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
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
                return new PaymentResponseDto(
                    $responseObject['responseCode'],
                    $responseObject['responseMessage'],
                    $responseObject['webRedirectUrl'],
                    isset($responseObject['referenceNo']) ? $responseObject['referenceNo'] : null,
                );
        }else{
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
            
        
    }

    public function doAccountUnbindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_ACCOUNT_UNBINDING_URL;
        $requestBody = $accountUnbindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                isset($responseObject['referenceNo']) ? $responseObject['referenceNo'] : null,
                ""
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }

    public function doCardUnbindingProcess(
        RequestHeaderDto $requestHeaderDto,
        AccountUnbindingRequestDto $accountUnbindingRequestDto,
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_CARD_UNBINDING_URL;
        $requestBody = $accountUnbindingRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return new AccountUnbindingResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                "",
                ""
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }
    public function encryptCbc(string $input, string $secretKey): string
    {
        try {
            $secretKey = $this->getSecretKey($secretKey);
            $iv = $this->generateIv(); // Menghasilkan IV

            // Mengenkripsi data
            $cipherText = openssl_encrypt($input, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
            if ($cipherText === false) {
                throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
            }

            // Menggabungkan ciphertext dan IV menjadi string base64
            $ivString = base64_encode($iv);
            return base64_encode($cipherText) . '|' . $ivString;
        } catch (\Exception $error) {
            // Menangani kesalahan
            echo 'Encryption error: ' . $error->getMessage();
            throw $error; // Anda bisa memilih untuk melempar ulang kesalahan
        }
    }
    public function getSecretKey(string $secretKey): string
    {
        if (strlen($secretKey) > 16) {
            return substr($secretKey, 0, 16);
        } elseif (strlen($secretKey) < 16) {
            return str_pad($secretKey, 16, '-');
        } else {
            return $secretKey;
        }
    }

    public function generateIv(): string
    {
        return openssl_random_pseudo_bytes(16); // 16-byte IV
    }
    public function doCardRegistrationProcess(
        RequestHeaderDto $requestHeaderDto,
        CardRegistrationRequestDto $requestDto,
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::CARD_REGISTRATION_URL;
        $requestBody = json_encode($requestDto);
        $headers = Helper::prepareHeaders($requestHeaderDto);
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            $additionalInfo = new CardRegistrationAdditionalInfoResponseDto(
                $responseObject['additionalInfo']['custIdMerchant'] ?? null,
                $responseObject['additionalInfo']['status'] ?? null,
                $responseObject['additionalInfo']['authCode'] ?? null
            );

            return new CardRegistrationResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                isset($responseObject['referenceNo']) ? $responseObject['referenceNo'] : null,
                $responseObject['redirectUrl'] ?? null,
                $additionalInfo
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }

    public function doRefundProcess(
        RequestHeaderDto $header, 
        RefundRequestDto $refundRequestDto, 
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_REFUND_URL;
        
        $headers = Helper::prepareHeaders($header);
        $requestBody = $refundRequestDto->generateJSONBody();
        
        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);

        // Validate the response
        if (!isset($responseObject['responseCode']) || !isset($responseObject['responseMessage'])) {
            throw new \Exception("Invalid response from refund API ".$response .json_encode($headers));
        }

       
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
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
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }

    public function doBalanceInquiryProcess(
        RequestHeaderDto $requestHeaderDto, 
        BalanceInquiryRequestDto $balanceInquiryRequestDto, 
        string $isProduction
    ) {
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_BALANCE_INQUIRY_URL;
        $requestBody = $balanceInquiryRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return new BalanceInquiryResponseDto(
                $responseObject['responseCode'],
                $responseObject['responseMessage'],
                $responseObject['accountInfos']
            );
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
        }
    }

    public function doCheckStatus(
        RequestHeaderDto $requestHeaderDto,
        DirectDebitCheckStatusRequestDto $checkStatusRequestDto,
        string $isProduction
    ){
        $baseUrl = Config::getBaseURL($isProduction);
        $apiEndpoint = $baseUrl . Config::DIRECT_DEBIT_CHECK_STATUS_URL;
        $requestBody = $checkStatusRequestDto->generateJSONBody();
        $headers = Helper::prepareHeaders($requestHeaderDto);

        $response = Helper::doHitAPI($apiEndpoint, $headers, $requestBody, 'POST');
        $responseObject = json_decode($response, true);
        $httpStatus = substr($responseObject['responseCode'], 0, 3);
        if (isset($responseObject['responseCode']) && $httpStatus === '200') {
            return $responseObject;
        } else {
            return [
                'responseCode' =>  $responseObject['responseCode'],
                'responseMessage' =>  $responseObject['responseMessage']
            ];
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
        $isValidSignature = $this->validateSymmetricSignature($xSignature, $stringToSign, $clientSecret);
        if (!$isValidSignature) {
            return [
                'responseCode' => "4010000",
                'responseMessage' =>  "Unauthorized. Invalid Signature"
            ];
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