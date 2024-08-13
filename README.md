


# Doku Snap SDK Integration Example

This repository demonstrates how to integrate and use the Doku Snap SDK for virtual account operations in your PHP project.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating a Virtual Account](#creating-a-virtual-account)
  - [Updating a Virtual Account](#updating-a-virtual-account)
  - [Deleting a Virtual Account](#deleting-a-virtual-account)
  - [Checking Virtual Account Status](#checking-virtual-account-status)
  - [Creating a Virtual Account (V1)](#creating-a-virtual-account-v1)
- [Authentication](#authentication)
- [Error Handling](#error-handling)

## Installation

To install the Doku Snap SDK, use Composer:

```bash
composer require doku/doku-php-library
```

## Configuration

To use the Doku Snap SDK, you need to initialize it with your credentials:

```php
use Doku\Snap\Snap;

$privateKey = "YOUR_PRIVATE_KEY";
$publicKey = "YOUR_PUBLIC_KEY";
$clientId = "YOUR_CLIENT_ID";
$issuer = "YOUR_ISSUER";
$isProduction = false; // Set to true for production environment
$secretKey = "YOUR_SECRET_KEY";

$Snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey);
```

## Usage

### Creating a Virtual Account

```php
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\Utilities\TotalAmount;
use Doku\Snap\Models\Utilities\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\CreateVaVirtualAccountConfig;

$createVaRequestDto = new CreateVaRequestDto(
    $partner,
    $virtualno,
    $partner . $virtualno,
    "Customer Name",
    "customer@example.com",
    "621234567890",
    "TRX_ID_" . time(),
    new TotalAmount("12500.00", "IDR"),
    new CreateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new CreateVaVirtualAccountConfig(true)),
    'C',
    "2024-08-01T09:54:04+07:00"
);

$virtualAccount = $Snap->createVa($createVaRequestDto);
```

### Updating a Virtual Account

```php
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\Utilities\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\Utilities\VirtualAccountConfig\UpdateVaVirtualAccountConfig;

$updateVaRequestDto = new UpdateVaRequestDto(
    $partnerServiceId,
    $customerNo,
    $virtualAccountNo,
    "Updated Customer Name",
    "updated@example.com",
    "621234567890",
    "TRX_ID_" . time(),
    new TotalAmount("14000.00", "IDR"),
    new UpdateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new UpdateVaVirtualAccountConfig("ACTIVE")),
    "1",
    "2024-07-24T15:54:04+07:00"
);

$updatedVirtualAccount = $Snap->updateVa($updateVaRequestDto);
```

### Deleting a Virtual Account

```php
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\Utilities\AdditionalInfo\DeleteVaRequestAdditionalInfo;

$deleteVaRequestDto = new DeleteVaRequestDto(
    $partnerServiceId,
    $customerNo,
    $virtualAccountNo,
    $trxId,
    new DeleteVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB")
);

$deletionResult = $Snap->deletePaymentCode($deleteVaRequestDto);
```

### Checking Virtual Account Status

```php
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;

$checkStatusVaRequestDto = new CheckStatusVaRequestDto(
    $partnerServiceId,
    $customerNo,
    $virtualAccountNo
);

$status = $Snap->checkStatusVa($checkStatusVaRequestDto);
```

### Creating a Virtual Account (V1)

```php
use Doku\Snap\Models\VA\Request\CreateVARequestDtoV1;

$createVaRequestDtoV1 = new CreateVaRequestDtoV1(
    $mallId,
    $chainMerchant,
    $amount,
    $purchaseAmount,
    $transIdMerchant,
    $paymentType,
    $words,
    $requestDateTime,
    $currency,
    $purchaseCurrency,
    $sessionId,
    $name,
    $email,
    $additionalData,
    $basket,
    $shippingAddress,
    $shippingCity,
    $shippingState,
    $shippingCountry,
    $shippingZipcode,
    $paymentChannel,
    $address,
    $city,
    $state,
    $country,
    $zipcode,
    $homephone,
    $mobilephone,
    $workphone,
    $birthday,
    $partnerServiceId,
    $expiredDate
);

$virtualAccountV1 = $Snap->createVaV1($createVaRequestDtoV1);
```

## Authentication

The SDK handles authentication automatically using the provided credentials. You can get a token for B2B operations:

### Get Current (Already Generated) Token String

```php
$token = $Snap->getCurrentTokenB2B();
```

### Get Newly Generated TokenB2BResponseDto
```php
$tokenB2BResponseDto = $Snap->getB2BToken(
    $privateKey, 
    $clientId, 
    $isProduction
);
```
## Create Token Helper
Example code:
```php
public function tokenRequest()
{
    try {
        $xSignature = $this->request->getHeaderLine('X-SIGNATURE');
        $xTimestamp = $this->request->getHeaderLine('X-TIMESTAMP');
        $xClientKey = $this->request->getHeaderLine('X-CLIENT-KEY');
        $jsonBody = $this->request->getJSON(true);

        $isSignatureValid = $this->snap->validateSignature($xSignature, $xTimestamp, $this->privateKey, $xClientKey);
        $notificationTokenDTO = $this->snap->generateTokenB2BResponse($isSignatureValid);
        $responseBody = $notificationTokenDTO->generateJSONBody();
        $headers = $notificationTokenDTO->generateJSONHeader();

        $tokenData = [
            'http_request_xRequestId' => time() * 1000,
            'http_request_header' => $this->request->getHeaders(),
            'http_request_body' => $this->request->getJSON(),
            'http_response_xrequestid' => time() * 1000,
            'http_response_header' => $headers,
            'http_response_body' => $responseBody,
            'prog_language' => "php",
            'versi_sdk' => "1.0.0"
        ];

        // Save to token table using the existing TokenModel
        $result = $this->tokenModel->createToken($tokenData);
        if (!$result) {
            throw new \Exception('Failed to insert token data');
        }

        // Set response headers
        foreach (json_decode($headers) as $name => $value) {
            $this->response->setHeader($name, $value);
        }

        return $this->respond($finalResponse, 200);

    } catch (\Exception $e) {
        // Handle any unexpected exceptions
        //...
    }
}
```

## Receive Notification Helper
Example Code:
```php
public function paymentNotification()
{
    $jsonBody = $this->request->getJSON(true);
    $xRequestId = time() * 1000;

    $requestHeaderDTO = new RequestHeaderDTO(
        $this->request->getHeaderLine('X-TIMESTAMP'),
        $this->request->getHeaderLine('X-SIGNATURE'),
        $this->request->getHeaderLine('X-CLIENT-KEY'),
        $xRequestId,
        "channel id",
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

    $data = [
        'http_request_xRequestId' => $xRequestId,
        'http_request_header' => json_encode($this->request->getHeaders()),
        'http_request_body' => json_encode($jsonBody),
        'http_response_xrequestid' => $xRequestId,
        'http_response_header' => $headers,
        'http_response_body' => $responseBody,
        'prog_language' => "php",
        'versi_sdk' => "1.0.5"
    ];

    try {
        $result = $this->notificationModel->createNotification($data);
        if (!$result) {
            // do other logic here
        }
    } catch (\Exception $e) {
        // do handling here
    }

    foreach (json_decode($headers) as $name => $value) {
        $this->response->setHeader($name, $value);
    }
    return $this->respond($responseBody);
}

```


## Direct Inquiry Helper
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
                // construct json body here
            ];
            $directInquiryData = [
                'inquiry_request_id' => $inquiryRequestId,
                'inquiry_json_object' => json_encode($body),
                'prog_language' => 'php',
                'versi_sdk' => '1.0.0'
            ];
            $incomingRequestData = [
                'request_inquiry_request_id' => $inquiryRequestId,
                'request_inquiry_body' => json_encode($requestBody),
                'request_inquiry_header' => json_encode($this->request->getHeaders()),
                'prog_language' => 'php',
                'versi_sdk' => '1.0.0'
            ];

            try {
                $result = $this->directInquiryModel->createInquiry($directInquiryData);
                $result = $this->incomingRequestDirectInquiryModel->createRequest($incomingRequestData);
            } catch (\Exception $e) {
                // handle exception
            }

            foreach ($this->request->getHeaders() as $name => $value) {
                $this->response->setHeader($name, $value);
            }

            return $this->respond($body);
        } else {
            // handle invalid token
        }
    }
```

For more detailed information about the Doku Snap SDK and its features, please refer to the official documentation. 
```

This README provides an overview of how to use the Doku Snap SDK for virtual account operations, including installation, configuration, and examples of various operations. You may want to customize it further based on your specific use case and add any additional information that might be relevant to your users.