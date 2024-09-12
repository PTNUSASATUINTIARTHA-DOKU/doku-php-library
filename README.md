# Doku Snap SDK Integration Guide

This guide demonstrates how to integrate and use the Doku Snap SDK for various payment operations in your PHP project.

## Table of Contents

# Doku Snap SDK Integration Guide

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Usage](#usage)
   1. [Initialization](#initialization)
   2. [Token Management](#token-management)
      - [Get Current Token](#get-current-token)
      - [Generate New Token](#generate-new-token)
   3. [Virtual Account Operations](#virtual-account-operations)
      - [Create Virtual Account](#create-virtual-account)
      - [Update Virtual Account](#update-virtual-account)
      - [Delete Virtual Account](#delete-virtual-account)
      - [Check Virtual Account Status](#check-virtual-account-status)
   4. [Direct Debit Operations](#direct-debit-operations)
      - [Account Binding](#account-binding)
      - [Account Unbinding](#account-unbinding)
      - [Payment Jump App](#payment-jump-app)
   5. [Card Operations](#card-operations)
      - [Card Registration](#card-registration)
      - [Card Unbinding](#card-unbinding)
   6. [Other Operations](#other-operations)
      - [Check Transaction Status](#check-transaction-status)
      - [Refund](#refund)
      - [Balance Inquiry](#balance-inquiry)
4. [Error Handling](#error-handling)
5. [Advanced Usage](#advanced-usage)
   1. [Handling Token Requests](#handling-token-requests)
   2. [Handling Payment Notifications](#handling-payment-notifications)
   3. [Handling Direct Inquiries](#handling-direct-inquiries)
   4. [Handling Notify Payment for Direct Debit](#handling-notify-payment-for-direct-debit)
6. [Best Practices for Controllers](#best-practices-for-controllers)

## Installation

To install the Doku Snap SDK, use Composer:

```bash
composer require doku/doku-php-library
```

## Configuration

Before using the Doku Snap SDK, you need to initialize it with your credentials:

```php
use Doku\Snap\Snap;

$privateKey = "YOUR_PRIVATE_KEY";
$publicKey = "YOUR_PUBLIC_KEY";
$clientId = "YOUR_CLIENT_ID";
$secretKey = "YOUR_SECRET_KEY";
$isProduction = false; // Set to true for production environment
$issuer = "YOUR_ISSUER"; // Optional
$authCode = "YOUR_AUTH_CODE"; // Optional

$snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey, $authCode);
```

## Usage

### Initialization

Always start by initializing the Snap object:

```php
$snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey, $authCode);
```

### Token Management

#### Get Current Token

```php
$currentToken = $snap->getCurrentTokenB2B();
echo "Current Token: " . $currentToken . PHP_EOL;
```

#### Generate New Token

```php
$newTokenResponse = $snap->getB2BToken($privateKey, $clientId, $isProduction);
echo "New Token: " . $newTokenResponse->accessToken . PHP_EOL;
```

### Virtual Account Operations

#### Create Virtual Account

```php
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig;

$createVaRequestDto = new CreateVaRequestDto(
    "8129014",  // partner
    "17223992157",  // customerno
    "812901417223992157",  // customerNo
    "T_" . time(),  // virtualAccountName
    "test.example." . time() . "@test.com",  // virtualAccountEmail
    "621722399214895",  // virtualAccountPhone
    "INV_CIMB_" . time(),  // trxId
    new TotalAmount("12500.00", "IDR"),  // totalAmount
    new CreateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new CreateVaVirtualAccountConfig(true)),  // additionalInfo
    'C',  // virtualAccountTrxType
    "2024-08-31T09:54:04+07:00"  // expiredDate
);

$result = $snap->createVa($createVaRequestDto);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Update Virtual Account

```php
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig;

$updateVaRequestDto = new UpdateVaRequestDto(
    "8129014",  // partnerServiceId
    "17223992155",  // customerNo
    "812901417223992155",  // virtualAccountNo
    "T_" . time(),  // virtualAccountName
    "test.example." . time() . "@test.com",  // virtualAccountEmail
    "00000062798",  // virtualAccountPhone
    "INV_CIMB_" . time(),  // trxId
    new TotalAmount("14000.00", "IDR"),  // totalAmount
    new UpdateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new UpdateVaVirtualAccountConfig("ACTIVE", "10000.00", "15000.00")),  // additionalInfo
    "O",  // virtualAccountTrxType
    "2024-08-02T15:54:04+07:00"  // expiredDate
);

$result = $snap->updateVa($updateVaRequestDto);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Delete Virtual Account

```php
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\AdditionalInfo\DeleteVaRequestAdditionalInfo;

$deleteVaRequestDto = new DeleteVaRequestDto(
    "8129014",  // partnerServiceId
    "17223992155",  // customerNo
    "812901417223992155",  // virtualAccountNo
    "INV_CIMB_" . time(),  // trxId
    new DeleteVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB")  // additionalInfo
);

$result = $snap->deletePaymentCode($deleteVaRequestDto);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Check Virtual Account Status

```php
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;

$checkStatusVaRequestDto = new CheckStatusVaRequestDto(
    "8129014",  // partnerServiceId
    "17223992155",  // customerNo
    "812901417223992155",  // virtualAccountNo
    null,
    null,
    null
);

$result = $snap->checkStatusVa($checkStatusVaRequestDto);
echo json_encode($result, JSON_PRETTY_PRINT);
```

### Direct Debit Operations

#### Account Binding

```php
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingAdditionalInfoRequestDto;

$additionalInfo = new AccountBindingAdditionalInfoRequestDto(
    "Mandiri",  // channel
    "CUST123",  // custIdMerchant
    "John Doe",  // customerName
    "john.doe@example.com",  // email
    "1234567890",  // idCard
    "Indonesia",  // country
    "123 Main St, Jakarta",  // address
    "19900101",  // dateOfBirth
    "https://success.example.com",  // successRegistrationUrl
    "https://fail.example.com",  // failedRegistrationUrl
    "iPhone 12",  // deviceModel
    "iOS",  // osType
    "CH001"  // channelId
);

$accountBindingRequestDto = new AccountBindingRequestDto(
    "6281234567890",  // phoneNo
    $additionalInfo
);

$result = $snap->doAccountBinding($accountBindingRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Account Unbinding

```php
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingAdditionalInfoRequestDto;

$additionalInfo = new AccountUnbindingAdditionalInfoRequestDto("Mandiri");

$accountUnbindingRequestDto = new AccountUnbindingRequestDto(
    "tokenB2b2c123",  // tokenId (tokenB2b2c)
    $additionalInfo
);

$result = $snap->doAccountUnbinding($accountUnbindingRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Payment Jump App

```php
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppAdditionalInfoRequestDto;
use Doku\Snap\Models\PaymentJumpApp\UrlParamDto;

$timestamp = time();
$totalAmount = new TotalAmount("50000.00", "IDR");

$additionalInfo = new PaymentJumpAppAdditionalInfoRequestDto(
    "Mandiri",  // channel
    "Payment for Order #123",  // remarks
    "merchantId"
);

$urlParam = new UrlParamDto("url", "type", "no");

$paymentJumpAppRequestDto = new PaymentJumpAppRequestDto(
    "ORDER_" . $timestamp,  // partnerReferenceNo
    date('Y-m-d H:i:s', strtotime('+1 day')),  // validUpTo (24 hours from now)
    "12",  // pointOfInitiation
    $urlParam,
    $totalAmount,
    $additionalInfo
);

$deviceId = "DEVICE_ID_123";
$result = $snap->doPaymentJumpApp($paymentJumpAppRequestDto, $deviceId, $privateKey, $clientId, $secretKey, $isProduction);
echo json_encode($result, JSON_PRETTY_PRINT);
```

### Card Operations

#### Card Registration

```php
use Doku\Snap\Models\CardRegistration\CardRegistrationRequestDto;
use Doku\Snap\Models\CardRegistration\CardRegistrationAdditionalInfoRequestDto;

$additionalInfo = new CardRegistrationAdditionalInfoRequestDto(
    'Mandiri',
    'John Doe',
    'john@example.com',
    '1234567890',
    'ID',
    '123 Main St',
    '19900101',
    'http://success.url',
    'http://failed.url'
);

$cardRegistrationRequestDto = new CardRegistrationRequestDto(
    'encrypted_card_data',
    'cust123',
    '081234567890',
    $additionalInfo
);

$deviceId = "DEVICE_ID_123";
$response = $snap->doCardRegistration(
    $cardRegistrationRequestDto,
    $deviceId,
    $privateKey,
    $clientId,
    $secretKey,
    $isProduction
);

echo json_encode($response, JSON_PRETTY_PRINT);
```

#### Card Unbinding

```php
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingAdditionalInfoRequestDto;

$additionalInfo = new AccountUnbindingAdditionalInfoRequestDto("Mandiri");

$cardUnbindingRequestDto = new AccountUnbindingRequestDto(
    "tokenB2b2c123",  // tokenId (tokenB2b2c)
    $additionalInfo
);

$result = $snap->doCardUnbinding($cardUnbindingRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
echo json_encode($result, JSON_PRETTY_PRINT);
```

### Other Operations

#### Check Transaction Status

```php
use Doku\Snap\Models\CheckStatus\CheckStatusRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoRequestDto;

$checkStatusRequestDto = new CheckStatusRequestDto(
    "originalPartnerRefNo123",     // originalPartnerReferenceNo
    "originalRefNo456",            // originalReferenceNo
    "originalExtId789",            // originalExternalId
    "SERVICE_CODE_001",            // serviceCode
    "2023-08-29T12:00:00+07:00",   // transactionDate
    new TotalAmount(100000, "IDR"),// totalAmount
    "MERCHANT_001",                // merchantId
    "SUBMERCHANT_001",             // subMerchantId
    "STORE_001",                   // externalStoreId
    new CheckStatusAdditionalInfoRequestDto("DEVICE_001", "DIRECT_DEBIT_MANDIRI") // additionalInfo
);

$authCode = "exampleAuthCode456";

$response = $snap->doCheckStatus(
    $checkStatusRequestDto,
    $authCode,
    $privateKey,
    $clientId,
    $secretKey,
    $isProduction
);

echo json_encode($response, JSON_PRETTY_PRINT);
```

#### Refund

```php
use Doku\Snap\Models\Refund\RefundRequestDto;
use Doku\Snap\Models\Refund\RefundAdditionalInfoRequestDto;

$additionalInfo = new RefundAdditionalInfoRequestDto("WEB");
$refundAmount = new TotalAmount("100.00", "USD");
$refundRequest = new RefundRequestDto(
    $additionalInfo,
    "ORIG123",
    "EXT456",
    $refundAmount,
    "Customer request",
    "REF789"
);

$authCode = "exampleAuthCode456";
$result = $snap->doRefund($refundRequest, $authCode, $privateKey, $clientId, $secretKey, $isProduction);
echo json_encode($result, JSON_PRETTY_PRINT);
```

#### Balance Inquiry

```php
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryRequestDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryAdditionalInfoRequestDto;

$additionalInfo = new BalanceInquiryAdditionalInfoRequestDto("DIRECT_DEBIT_MANDIRI");
$balanceInquiryRequestDto = new BalanceInquiryRequestDto($additionalInfo);

$authCode = "exampleAuthCode123";
$result = $snap->doBalanceInquiry($balanceInquiryRequestDto, $authCode);

echo "Response Code: " . $result->responseCode . PHP_EOL;
echo "Response Message: " . $result->responseMessage . PHP_EOL;
echo "Account Infos: " . PHP_EOL;

foreach ($result->accountInfos as $accountInfo) {
    echo "  Balance Type: " . $accountInfo->balanceType . PHP_EOL;
    echo "  Amount: " . $accountInfo->amount->value . " " . $accountInfo->amount->currency . PHP_EOL;
    echo "  Flat Amount: " . $accountInfo->flatAmount->value . " " . $accountInfo->flatAmount->currency . PHP_EOL;
    echo "  Hold Amount: " . $accountInfo->holdAmount->value . " " . $accountInfo->holdAmount->currency . PHP_EOL;
    echo "---" . PHP_EOL;
}
```

## Error Handling

The SDK throws exceptions for various error conditions. Always wrap your API calls in try-catch blocks:

```php
try {
    $result = $snap->createVa($createVaRequestDto);
    // Process successful result
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    // Handle the error appropriately
}
```

## Advanced Usage

### Handling Token Requests

To handle token requests in a controller (e.g., in CodeIgniter):

```php
public function tokenRequest()
{
    try {
        $xSignature = $this->request->getHeaderLine('X-SIGNATURE');
        $xTimestamp = $this->request->getHeaderLine('X-TIMESTAMP');
        $xClientKey = $this->request->getHeaderLine('X-CLIENT-KEY');
        $jsonBody = $this->request->getJSON(true);

        // Validate headers and body
        if (empty($xSignature) || empty($xTimestamp) || empty($xClientKey)) {
            return $this->failUnauthorized('Missing required headers');
        }

        if (!isset($jsonBody['grantType']) || $jsonBody['grantType'] !== 'client_credentials') {
            return $this->failValidationError('Invalid or missing grantType in request body');
        }

        $isSignatureValid = $this->snap->validateSignature($xSignature, $xTimestamp, $this->privateKey, $xClientKey);
        $notificationTokenDTO = $this->snap->generateTokenB2BResponse($isSignatureValid);
        $responseBody = $notificationTokenDTO->generateJSONBody();
        $headers = $notificationTokenDTO->generateJSONHeader();

        // Set response headers
        foreach (json_decode($headers) as $name => $value) {
            $this->response->setHeader($name, $value);
        }

        return $this->respond(json_decode($responseBody), 200);

    } catch (\Exception $e) {
        return $this->fail(['error' => $e->getMessage()], 500);
    }
}
```

### Handling Payment Notifications

To handle payment notifications:

```php
use Doku\Snap\Models\RequestHeader\RequestHeaderDTO;
use Doku\Snap\Models\Notification\PaymentNotificationRequestBodyDTO;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\Notification\PaymentNotificationRequestAdditionalInfo;

public function paymentNotification()
{
    $jsonBody = $this->request->getJSON(true);
    $xRequestId = time() * 1000;

    $requestHeaderDTO = new RequestHeaderDTO(
        $this->request->getHeaderLine('X-TIMESTAMP'),
        $this->request->getHeaderLine('X-SIGNATURE'),
        $this->request->getHeaderLine('X-CLIENT-KEY'),
        $xRequestId,
        "channel_id",
        $this->request->getHeaderLine('Authorization')
    );

    $paidAmount = new TotalAmount(
        $jsonBody['paidAmount']['value'] ?? null,
        $jsonBody['paidAmount']['currency'] ?? null
    );

    $additionalInfo = new PaymentNotificationRequestAdditionalInfo(
        $jsonBody['additionalInfo']['channel'] ?? null,
        $jsonBody['additionalInfo']['senderName'] ?? null,
        $jsonBody['additionalInfo']['sourceAccountNo'] ?? null,
        $jsonBody['additionalInfo']['sourceBankCode'] ?? null,
        $jsonBody['additionalInfo']['sourceBankName'] ?? null
    );

    $paymentNotificationRequestBodyDTO = new PaymentNotificationRequestBodyDTO(
        $jsonBody['partnerServiceId'] ?? '',
        $jsonBody['customerNo'] ?? '',
        $jsonBody['virtualAccountNo'] ?? '',
        $jsonBody['virtualAccountName'] ?? '',
        $jsonBody['virtualAccountEmail'] ?? '',
        $jsonBody['trxId'] ?? '',
        $jsonBody['paymentRequestId'] ?? '',
        $paidAmount,
        $jsonBody['virtualAccountPhone'] ?? '',
        $additionalInfo,
        $jsonBody['trxDateTime'] ?? $jsonBody['expiredDate'] ?? '',
        $jsonBody['virtualAccountTrxType'] ?? ''
    );

    $responseDTO = $this->snap->validateTokenAndGenerateNotificationResponse($requestHeaderDTO, $paymentNotificationRequestBodyDTO);
    $headers = $responseDTO->generateJSONHeader();
    $responseBody = $responseDTO->generateJSONBody();

    foreach (json_decode($headers) as $name => $value) {
        $this->response->setHeader($name, $value);
    }
    return $this->respond(json_decode($responseBody));
}
```

### Handling Direct Inquiries

To handle direct inquiries:

```php
public function inquiry()
{
    $authorization = $this->request->getHeaderLine('Authorization');
    $isValid = $this->snap->validateTokenB2B($authorization);

    if ($isValid) {
        $requestBody = $this->request->getJSON(true);
        $inquiryRequestId = $requestBody['inquiryRequestId'];

        $header = $this->snap->generateRequestHeader();
        $body = [
            "responseCode" => "2002400",
            "responseMessage" => "Successful",
            "virtualAccountData" => [
                "partnerServiceId" => "12362",
                "customerNo" => "60000000000000000001",
                "virtualAccountNo" => "1236260000000000000000001",
                "virtualAccountName" => "Customer Name",
                "virtualAccountEmail" => "customer.email@mail.com",
                "virtualAccountPhone" => "081293912081",
                "totalAmount" => [
                    "value" => "11500.00",
                    "currency" => "IDR"
                ],
                "virtualAccountTrxType" => "C",
                "expiredDate" => "2023-01-01T10:55:00+07:00",
                "additionalInfo" => [
                    "channel" => "VIRTUAL_ACCOUNT_BRI",
                    "trxId" => "INV-001"
                ],
                "inquiryStatus" => "00",
                "inquiryReason" => [
                    "english" => "Success",
                    "indonesia" => "Sukses"
                ],
                "inquiryRequestId" => $inquiryRequestId,
            ]
        ];

        foreach ($this->request->getHeaders() as $name => $value) {
            $this->response->setHeader($name, $value);
        }

        return $this->respond($body);
    } else {
        return $this->failUnauthorized('Invalid token');
    }
}
```

### Handling Notify Payment for Direct Debit

To handle notify payment for direct debit:

```php
use Doku\Snap\Models\NotifyPayment\NotifyPaymentDirectDebitRequestDto;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\NotifyPayment\PaymentNotificationAdditionalInfoRequestDto;
use Doku\Snap\Models\VA\AdditionalInfo\Origin;
use Doku\Snap\Models\Payment\LineItemsDto;

public function handleDirectDebitNotification()
{
    $requestBody = $this->request->getJSON(true);
    $xSignature = $this->request->getHeaderLine('X-SIGNATURE');
    $xTimestamp = $this->request->getHeaderLine('X-TIMESTAMP');

    $amount = new TotalAmount($requestBody['amount']['value'], $requestBody['amount']['currency']);

    $lineItems = array_map(function ($item) {
        return new LineItemsDto($item['name'], $item['price'], $item['quantity']);
    }, $requestBody['additionalInfo']['lineItems']);

    $additionalInfo = new PaymentNotificationAdditionalInfoRequestDto(
        $requestBody['additionalInfo']['channelId'],
        $requestBody['additionalInfo']['acquirerId'],
        $requestBody['additionalInfo']['custIdMerchant'],
        $requestBody['additionalInfo']['accountType'],
        $lineItems,
        new Origin()
    );

    $notifyPaymentRequest = new NotifyPaymentDirectDebitRequestDto(
        $requestBody['originalPartnerReferenceNo'],
        $requestBody['originalReferenceNo'],
        $requestBody['originalExternalId'],
        $requestBody['latestTransactionStatus'],
        $requestBody['transactionStatusDesc'],
        $amount,
        $additionalInfo
    );

    $response = $this->snap->handleDirectDebitNotification($notifyPaymentRequest, $xSignature, $xTimestamp);

    return $this->respond($response);
}
```

## Best Practices for Controllers

1. **Validate Input**: Always validate and sanitize input data before processing.
2. **Use Try-Catch**: Wrap your SDK calls in try-catch blocks to handle exceptions gracefully.
3. **Log Errors**: Log any errors or exceptions for debugging purposes.
4. **Set Appropriate Headers**: Ensure you're setting the correct headers in your responses.
5. **Handle Token Expiration**: Implement logic to refresh tokens when they expire.
6. **Secure Sensitive Data**: Never log or expose sensitive data like tokens or personal information.

By following these patterns and best practices, you can effectively integrate the Doku Snap SDK into your CodeIgniter or similar MVC framework application.