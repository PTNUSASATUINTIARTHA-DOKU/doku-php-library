


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
composer require doku/snap-sdk
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

```php
$tokenAndTime = $Snap->getTokenAndTime();
```

## Error Handling

The SDK throws exceptions for various error conditions. It's recommended to wrap your API calls in try-catch blocks to handle potential errors:

```php
try {
    $virtualAccount = $Snap->createVa($createVaRequestDto);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

For more detailed information about the Doku Snap SDK and its features, please refer to the official documentation.
```

This README provides an overview of how to use the Doku Snap SDK for virtual account operations, including installation, configuration, and examples of various operations. You may want to customize it further based on your specific use case and add any additional information that might be relevant to your users.