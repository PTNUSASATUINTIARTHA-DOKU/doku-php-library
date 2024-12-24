# DOKU PHP SDK Documentation

## Introduction
Welcome to the DOKU PHP SDK! This SDK simplifies access to the DOKU API for your server-side PHP applications, enabling seamless integration with payment and virtual account services.

If your looking for another language, we got: [Node.js](#), [Go](#), [Python](#), [Java](#)

## Table of Contents
- [DOKU PHP SDK Documentation](#doku-php-sdk-documentation)
  - [1. Getting Started](#1-getting-started)
  - [2. Usage](#2-usage)
    - [Virtual Account](#virtual-account)
      - [I. Virtual Account (DGPC \& MGPC)](#i-virtual-account-dgpc--mgpc)
      - [II. Virtual Account (DIPC)](#ii-virtual-account-dipc)
      - [III. Check Virtual Account Status](#iii-check-virtual-account-status)
    - [B. Binding / Registration Operations](#b-binding--registration-operations)
      - [I. Account Binding](#i-account-binding)
      - [II. Card Registration](#ii-card-registration)
    - [C. Direct Debit](#c-direct-debit)
      - [I. Request Payment](#i-request-payment)
    - [D. E-Wallet](#d-e-wallet)
      - [I. Request Payment](#i-request-payment-1)
  - [3. Other Operation](#3-other-operation)
    - [Check Transaction Status](#a-check-transaction-status)
    - [Refund](#b-refund)
    - [Balance Inquiry](#c-balance-inquiry)
  - [4. Error Handling and Troubleshooting](#4-error-handling-and-troubleshooting)
  - [5. Appendix](#4-appendix)



## 1. Getting Started

### Requirements
- PHP version 7.4 or higher
- Composer installed

### Installation
To install the Doku Snap SDK, use Composer:
```bash
composer require doku/doku-php-library
```

### Configuration
Before using the Doku Snap SDK, you need to initialize it with your credentials:

1. **Client ID** and **Secret Key**: Retrieve these from the Integration menu in your [Doku Dashboard](#)
2. **Private Key**: Generate your Private Key following [Doku Guide](#)

| **Parameter**       | **Description**                                    | **Required** |
|-----------------|----------------------------------------------------|--------------|
| `privateKey`    | The private key for the partner service.           | ✅          |
| `publicKey`     | The public key for the partner service.            | ✅           |
| `clientId`      | The client ID associated with the service.         | ✅           |
| `secretKey`     | The secret key for the partner service.            | ✅           |
| `isProduction`  | Set to true for production environment             | ✅           |
| `issuer`        | Optional issuer for advanced configurations.       | ❌           |
| `authCode`      | Optional authorization code for advanced use.      | ❌           |


```php
use Doku\Snap\Snap;

$privateKey = "YOUR_PRIVATE_KEY";
$publicKey = "YOUR_PUBLIC_KEY";
$clientId = "YOUR_CLIENT_ID";
$secretKey = "YOUR_SECRET_KEY";
$isProduction = false;
$issuer = "YOUR_ISSUER"; 
$authCode = "YOUR_AUTH_CODE"; 

$snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey, $authCode);
```

## 2. Usage

**Initialization**
Always start by initializing the Snap object.

```php
$snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey, $authCode);
```
### Virtual Account
#### I. Virtual Account (DGPC & MGPC)
##### DGPC
- **Description:** A pre-generated virtual account provided by DOKU.
- **Use Case:** Recommended for one-time transactions.
##### MGPC
- **Description:** Merchant generated virtual account.
- **Use Case:** Recommended for top up business model.

Parameters for **createVA** and **updateVA**
<table>
  <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>partnerServiceId</code></td>
      <td colspan="2">The unique identifier for the partner service.</td>
      <td>String(20)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>customerNo</code></td>
      <td colspan="2">The customer's identification number.</td>
      <td>String(20)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>virtualAccountNo</code></td>
      <td colspan="2">The virtual account number associated with the customer.</td>
      <td>String(20)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>virtualAccountName</code></td>
      <td colspan="2">The name of the virtual account associated with the customer.</td>
      <td>String(255)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>virtualAccountEmail</code></td>
      <td colspan="2">The email address associated with the virtual account.</td>
      <td>String(255)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>virtualAccountPhone</code></td>
      <td colspan="2">The phone number associated with the virtual account.</td>
      <td>String(9-30)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>trxId</code></td>
      <td colspan="2">Invoice number in Merchants system.</td>
      <td>String(64)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="2"><code>totalAmount</code></td>
      <td colspan="2"><code>value</code>: Transaction Amount (ISO 4217) <br> <small>Example: "11500.00"</small></td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>Currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="4"><code>additionalInfo</code></td>
      <td colspan="2"><code>channel</code>: Channel that will be applied for this VA <br> <small>Example: VIRTUAL_ACCOUNT_BANK_CIMB</small></td>
      <td>String(20)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="3"><code>virtualAccountConfig</code></td>
      <td><code>reusableStatus</code>: Reusable Status For Virtual Account Transaction <br><small>value TRUE or FALSE</small></td>
      <td>Boolean</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>minAmount</code>: Minimum Amount can be used only if <code>virtualAccountTrxType</code> is Open Amount (O). <br><small>Example: "10000.00"</small></td>
      <td>String(16.2)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>maxAmount</code>: Maximum Amount can be used only if <code>virtualAccountTrxType</code> is Open Amount (O). <br><small>Example: "5000000.00"</small></td>
      <td>String(16.2)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>virtualAccountTrxType</code></td>
      <td colspan="2">Transaction type for this transaction. C (Closed Amount), O (Open Amount)</td>
      <td>String(1)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>expiredDate</code></td>
      <td colspan="2">Expiration date for Virtual Account. ISO-8601 <br><small>Example: "2023-01-01T10:55:00+07:00"</small></td>
      <td>String</td>
      <td>❌</td>
    </tr>
  </tbody>
</table>


1. **Create Virtual Account**
    - **Function:** `createVa`
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
        new CreateVaRequestAdditionalInfo(
              "VIRTUAL_ACCOUNT_BANK_CIMB", new CreateVaVirtualAccountConfig(true)
              ), // additionalInfo
        'C',  // virtualAccountTrxType
        "2024-08-31T09:54:04+07:00"  // expiredDate
      );

      $result = $snap->createVa($createVaRequestDto);
      echo json_encode($result, JSON_PRETTY_PRINT);
    ```

2. **Update Virtual Account**
    - **Function:** `updateVa`

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

3. **Delete Virtual Account**

    | **Parameter**        | **Description**                                                             | **Data Type**       | **Required** |
    |-----------------------|----------------------------------------------------------------------------|---------------------|--------------|
    | `partnerServiceId`    | The unique identifier for the partner service.                             | String(8)        | ✅           |
    | `customerNo`          | The customer's identification number.                                      | String(20)       | ✅           |
    | `virtualAccountNo`    | The virtual account number associated with the customer.                   | String(20)       | ✅           |
    | `trxId`               | Invoice number in Merchant's system.                                       | String(64)       | ✅           |
    | `additionalInfo`      | `channel`: Channel applied for this VA.<br><small>Example: VIRTUAL_ACCOUNT_BANK_CIMB</small> | String(30)       | ✅    |

    
  - **Function:** `deletePaymentCode`

    ```php
    use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
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


#### II. Virtual Account (DIPC)
- **Description:** Custom virtual account codes created by the merchant.
- **Use Case:** Useful when merchants require custom payment identifiers.
- **Function:** `directInquiryVa`

    ```php
        use Doku\Snap\Models\DirectInquiry\InquiryResponseBodyDto;
        use Doku\Snap\Models\DirectInquiry\InquiryResponseVirtualAccountDataDto;
        use Doku\Snap\Models\DirectInquiry\InquiryReasonDto;
        use Doku\Snap\Models\DirectInquiry\InquiryResponseAdditionalInfoDto;
        use Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig;
        use Doku\Snap\Models\TotalAmount\TotalAmount;
        
        directInquiry(){
          $requestBody = $this->request->getJSON(true);
        $authorization = $this->request->getHeaderLine('Authorization');
        $isValid = $this->snap->validateTokenB2B($authorization);
        if ($isValid) {
           
            $responseCode =2002400;
            $responseMessage = 'Successful';
            $inquiryRequestId = $requestBody['inquiryRequestId'];


            $partnerServiceId = $requestBody['partnerServiceId'];
            $customerNo = $requestBody['customerNo'];
            $virtualAccountNo = $requestBody['virtualAccountNo'];
            $virtualAccountName = "Nama ". time();
            $trxId =  "INV_MERCHANT_" . time();
            $virtualAccountEmail = "email." . time() . "@gmail.com";
            $virtualAccountPhone =time();
            $totalAmount = new TotalAmount(
                "25000.00",
               "IDR"
            );
            $inquiryStatus = "00";
            $additionalInfo = new InquiryResponseAdditionalInfoDto(
                $requestBody['additionalInfo']['channel'],
                $trxId,
                new CreateVaVirtualAccountConfig(
                    true,
                    "100000.00",
                    "10000.00"
                )
            );
            $inquiryReason = new InquiryReasonDto(
                "Success",
                "Sukses"
            );
            $virtualAccountTrxType = "C";
            $freeText = [
                [
                    "english" => "Free text",
                    "indonesia" => "Tulisan Bebas"
                ]
            ];
            $vaData = new InquiryResponseVirtualAccountDataDto(
                $partnerServiceId,
                $customerNo,
                $virtualAccountNo,
                $virtualAccountName,
                $virtualAccountEmail,
                $virtualAccountPhone,
                $totalAmount,
                $virtualAccountTrxType,
                $additionalInfo,
                $inquiryStatus,
                $inquiryReason,
                $inquiryRequestId,
                $freeText

            );
            $body = new InquiryResponseBodyDto(
                    $responseCode,
                    $responseMessage,
                    $vaData
            );
            return $this->respond($body);
        }
        }
    ```

#### III. Check Virtual Account Status
 | **Parameter**        | **Description**                                                             | **Data Type**       | **Required** |
|-----------------------|----------------------------------------------------------------------------|---------------------|--------------|
| `partnerServiceId`    | The unique identifier for the partner service.                             | String(8)        | ✅           |
| `customerNo`          | The customer's identification number.                                      | String(20)       | ✅           |
| `virtualAccountNo`    | The virtual account number associated with the customer.                   | String(20)       | ✅           |
| `inquiryRequestId`    | The customer's identification number.                                      | String(128)       | ❌           |
| `paymentRequestId`    | The virtual account number associated with the customer.                   | String(128)       | ❌           |
| `additionalInfo`      | The virtual account number associated with the customer.                   | String      | ❌           |

  - **Function:** `checkStatusVa`
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

    $result = $snap-> ($checkStatusVaRequestDto);
    echo json_encode($result, JSON_PRETTY_PRINT);
    ```

### B. Binding / Registration Operations
The card registration/account binding process must be completed before payment can be processed. The merchant will send the card registration request from the customer to DOKU.

Each card/account can only registered/bind to one customer on one merchant. Customer needs to verify OTP and input PIN.

| **Services**     | **Binding Type**      | **Details**                        |
|-------------------|-----------------------|-----------------------------------|
| Direct Debit      | Account Binding       | Supports **Allo Bank** and **CIMB** |
| Direct Debit      | Card Registration     | Supports **BRI**                    |
| E-Wallet          | Account Binding       | Supports **OVO**                    |

#### I. Account Binding 
1. **Binding**

<table>
  <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>phoneNo</code></td>
      <td colspan="2">Phone Number Customer. <br> <small>Format: 628238748728423</small> </td>
      <td>String(9-16)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="13"><code>additionalInfo</code></td>
      <td colspan="2"><code>channel</code>:  <br> <small>Example: "11500.00"</small></td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>custIdMerchant</code>: Customer id from merchant</td>
      <td>String(64)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>customerName</code>: Customer name from merchant</td>
      <td>String(70)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan="2"><code>email</code>: Customer email from merchant </td>
      <td>String(64)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan="2"><code>idCard</code>: </td>
      <td>String(20)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan="2"><code>country</code>: </td>
      <td>String(60)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan="2"><code>address</code>: </td>
      <td>String(255)</td>
      <td>❌</td>
    </tr>
        <tr>
      <td colspan="2"><code>dateOfBirth</code>: </td>
      <td>String(YYYYMMDD)</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan="2"><code>successRegistrationUrl</code>: </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>failedRegistrationUrl</code>: </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>deviceModel</code>: </td>
      <td>String(64)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>osType</code>: </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>channelId</code>:  </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    </tbody>
  </table> 

  - **Function:** `doAccountBinding`

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

1. **Unbinding**
    - **Function:** `doAccountUnbinding`
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

#### II. Card Registration
1. **Registration**
    - **Function:** `doCardRegistration`

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

2. **UnRegistration**
    - **Function:** `doCardUnbinding`

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

### C. Direct Debit

#### I. Request Payment
  After customer's account / card is bind merchant can send payment request from customer to DOKU. [How to bind](#b-binding--registration-operations)

| **Acquirer**       | **Channel Name**         | 
|-------------------|--------------------------|
| Allo Bank         | DIRECT_DEBIT_ALLO_SNAP   | 
| BRI               | DIRECT_DEBIT_BRI_SNAP    | 
| CIMB              | DIRECT_DEBIT_CIMB_SNAP   |

##### Common Parameters
The following fields are common across **Allo Bank, BRI and CIMB** requests:
<table>
  <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>partnerReferenceNo</code></td>
      <td colspan="2"> Reference No From Partner <br> <small>Format: 628238748728423</small> </td>
      <td>String(9-16)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="2"><code>amount</code></td>
      <td colspan="2"><code>value</code>: Transaction Amount (ISO 4217) <br> <small>Example: "11500.00"</small></td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>Currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="3"><code>additionalInfo</code> </td>
      <td colspan = "2" ><code>channel</code>: payment channel</td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>successPaymentUrl</code>: Redirect Url if payment success</td>
      <td>String</td>
      <td>✅</td>
    </tr>
        <tr>
      <td colspan="2"><code>failedPaymentUrl</code>: Redirect Url if payment fail
      </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    </tbody>
  </table> 

  #####  Allo Bank
  Allo Bank spesific parameters
  <table>
    <thead>
      <tr>
        <th><strong>Parameter</strong></th>
        <th colspan="2"><strong>Description</strong></th>
        <th><strong>Data Type</strong></th>
        <th><strong>Required</strong></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td rowspan = "4"><code>additionalInfo</code></td>
        <td colspan = "2" ><code>remarks</code>: Remarks from Partner</td>
        <td>String(40)</td>
        <td>✅</td>
      </tr>
      <tr>
        <td rowspan = "3" ><code>lineItems</code></td>
        <td><code>name</code>: Item Name</td>
         <td>String(32)</td>
         <td>✅</td>
      </tr>
      <tr>
        <td><code>price</code>: Amount (ISO 4217) <br> <small>Example: "11500.00"</small> </td>
        <td>String(16.2)</td>
        <td>✅</td>
      </tr>
       <tr>
        <td><code>quanity</code></td>
        <td>Integer</td>
        <td>✅</td>
      </tr>
      <tr>
        <td rowspan = "3"><code>payOptionDetails</code></td>
        <td colspan = "2" ><code>payMethod</code>: Balance Type Description <br> <small>option : BALANCE/POINT/PAYLATER</small> </td>
        <td>string</td>
        <td>✅</td>
      </tr>
      <tr>
        <td rowspan = "2" ><code>transAmount</code></td>
        <td><code>value</code>: Transaction Amount</td>
         <td>String(16.2)</td>
         <td>✅</td>
      </tr>
      <tr>
        <td><code>currency</code>: <small>example : IDR</small> </td>
        <td>String</td>
        <td>✅</td>
      </tr>
    </tbody>
  </table> 
   ##### CIMB
  CIMB spesific parameters
  <table>
    <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td><code>additionalInfo</code></td>
      <td colspan = "2" ><code>remarks</code>: Remarks from Partner</td>
      <td>String(40)</td>
      <td>✅</td>
    </tr>
    </tbody>
  </table> 
  - **Function:** `Payment Function doPayment for Direct Debit`
    ```php
     use Doku\Snap\Models\TotalAmount\TotalAmount;
     use Doku\Snap\Models\Payment\PaymentRequestDto;
     use Doku\Snap\Models\Payment\PaymentAdditionalInfoRequestDto;
     public function directDebitPayment(){
        $requestData = $this->request->getJSON(true);
        $payOptionDetails =json_decode(json_encode($requestData['payOptionDetails'] ?? null));
        $partnerReferenceNo = $requestData['partnerReferenceNo'] ?? null;
        $amount = new TotalAmount(
            $requestData['amount']['value'],
            $requestData['amount']['currency']
        );
        $additionalInfo = new PaymentAdditionalInfoRequestDto(
            $requestData['additionalInfo']['channel'],
            $requestData['additionalInfo']['remarks'],
            $requestData['additionalInfo']['successPaymentUrl'],
            $requestData['additionalInfo']['failedPaymentUrl'],
            $requestData['additionalInfo']['lineItems'],
            $requestData['additionalInfo']['paymentType'] ?? null
        );
        $feeType = $requestData['feeType'] ?? '';
        $chargeToken = $requestData['chargeToken'] ?? '';
        $request = new PaymentRequestDto(
            $partnerReferenceNo,
            $amount,
            $payOptionDetails,
            $additionalInfo,
            $feeType,
            $chargeToken
        );
        $ipAddress = $this->request->getHeaderLine('X-IP-ADDRESS');
        $authCode = $requestData['authCode'];
       
        $response = $this->snap->doPayment($request, $authCode, $ipAddress);
        if (is_array($response) || is_object($response)) {
            $responseObject = (array)$response; // Ubah objek ke array jika perlu
        } else {
            throw new \Exception('Unexpected response type');
        }
        // var_dump($responseObject);
        // Ambil responseCode
        $responseCode = $responseObject['responseCode'];

        // Atur status HTTP berdasarkan tiga angka pertama
        $statusCode = substr($responseCode, 0, 3);
        $this->response->setStatusCode((int)$statusCode); // Set status HTTP
        return $this->response->setJSON($responseObject);

        
    }

### D. E-Wallet

#### I. Request Payment
| **Acquirer**       | **Channel Name**        | 
|-------------------|--------------------------|
| DANA              | EMONEY_DANA_SNAP   | 
| OVO               | EMONEY_OVO_SNAP   | 
| ShopeePay         | EMONEY_SHOPEE_PAY_SNAP  |

The following fields are common across **DANA and ShopeePay** requests:
<table>
  <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>partnerReferenceNo</code></td>
      <td colspan="2"> Reference No From Partner <br> <small>Examplae : INV-0001</small> </td>
      <td>String(9-16)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>validUpto</code></td>
      <td colspan = "2" >Expired time payment url </td>
      <td>String</td>
      <td>❌</td>
    </tr>
    <tr>
      <td><code>pointOfInitiation</code></td>
      <td colspan = "2" >Point of initiation from partner,<br> value: app/pc/mweb </td>
      <td>String</td>
      <td>❌</td>
    </tr>
    <tr>
      <td rowspan = "3" > <code>urlParam</code></td>
      <td colspan = "2"><code>url</code>: URL after payment sucess </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>type</code>: Pay Return<br> <small>always PAY_RETURN </small></td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>isDeepLink</code>: Is Merchant use deep link or not<br> <small>Example: "Y/N"</small></td>
      <td>String(1)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="2"><code>amount</code></td>
      <td colspan="2"><code>value</code>: Transaction Amount (ISO 4217) <br> <small>Example: "11500.00"</small></td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>Currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>additionalInfo</code> </td>
      <td colspan = "2" ><code>channel</code>: payment channel</td>
      <td>String</td>
      <td>✅</td>
    </tr>
    </tbody>
  </table> 

##### DANA

DANA spesific parameters
<table>
    <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td rowspan = "2" ><code>additionalInfo</code></td>
      <td colspan = "2" ><code>orderTitle</code>: Order title from merchant</td>
      <td>String</td>
      <td>❌</td>
    </tr>
    <tr>
      <td colspan = "2" ><code>supportDeepLinkCheckoutUrl</code> : Value 'true' for Jumpapp behaviour, 'false' for webview, false by default</td>
      <td>String</td>
      <td>❌</td>
    </tr>
    </tbody>
  </table> 

##### OVO
After customer's account is bind merchant can send payment request from customer to DOKU. [How to bind](#b-binding--registration-operations)

<table>
  <thead>
    <tr>
      <th><strong>Parameter</strong></th>
      <th colspan="2"><strong>Description</strong></th>
      <th><strong>Data Type</strong></th>
      <th><strong>Required</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>partnerReferenceNo</code></td>
      <td colspan="2"> Reference No From Partner <br> <small>Examplae : INV-0001</small> </td>
      <td>String(9-16)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td><code>feeType</code></td>
      <td colspan = "2" >Fee type from partner | value should be: OUR/BEN/SHA </td>
      <td>String</td>
      <td>❌</td>
    </tr>
    <tr>
      <td rowspan="2"><code>amount</code></td>
      <td colspan="2"><code>value</code>: Transaction Amount (ISO 4217) <br> <small>Example: "11500.00"</small></td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>Currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
     <tr>
      <td rowspan = "5" > <code>payOptionDetails</code></td>
      <td colspan = "2"><code>payMethod</code>: Pay method format: CASH / POINTS </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="2"><code>transAmount</code></td>
      <td><code>value</code>Transaction Amount. Total Amount with 2 decimal</td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td ><code>currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan="2"><code>feeAmount</code></td>
      <td><code>value</code>: Fee Amount</td>
      <td>String(16.2)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td ><code>currency</code>: Currency <br> <small>Example: "IDR"</small></td>
      <td>String(3)</td>
      <td>✅</td>
    </tr>
    <tr>
      <td rowspan = "4" ><code>additionalInfo</code> </td>
      <td colspan = "2" ><code>channel</code>: payment channel</td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>successPaymentUrl</code>: Redirect Url if payment success
      </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    <tr>
      <td colspan="2"><code>failedPaymentUrl</code>: Redirect Url if payment fail
      </td>
      <td>String</td>
      <td>✅</td>
    </tr>
        <tr>
      <td colspan="2"><code>paymentType</code>: Transaction Type 
      <br> <small>value should be SALE/RECURRING </small>
      </td>
      <td>String</td>
      <td>✅</td>
    </tr>
    </tbody>
  </table> 

- **Function:** `Payment Function doPayment for Dana, OVO & Shopepay`
    ```php
     use Doku\Snap\Models\TotalAmount\TotalAmount;
     use Doku\Snap\Models\Payment\PaymentRequestDto;
     use Doku\Snap\Models\Payment\PaymentAdditionalInfoRequestDto;
     public function directDebitPayment(){
        $requestData = $this->request->getJSON(true);
        $payOptionDetails =json_decode(json_encode($requestData['payOptionDetails'] ?? null));
        $partnerReferenceNo = $requestData['partnerReferenceNo'] ?? null;
        $amount = new TotalAmount(
            $requestData['amount']['value'],
            $requestData['amount']['currency']
        );
        $additionalInfo = new PaymentAdditionalInfoRequestDto(
            $requestData['additionalInfo']['channel'],
            $requestData['additionalInfo']['remarks'],
            $requestData['additionalInfo']['successPaymentUrl'],
            $requestData['additionalInfo']['failedPaymentUrl'],
            $requestData['additionalInfo']['lineItems'],
            $requestData['additionalInfo']['paymentType'] ?? null
        );
        $feeType = $requestData['feeType'] ?? '';
        $chargeToken = $requestData['chargeToken'] ?? '';
        $request = new PaymentRequestDto(
            $partnerReferenceNo,
            $amount,
            $payOptionDetails,
            $additionalInfo,
            $feeType,
            $chargeToken
        );
        $ipAddress = $this->request->getHeaderLine('X-IP-ADDRESS');
        $authCode = $requestData['authCode'];
       
        $response = $this->snap->doPayment($request, $authCode, $ipAddress);
        if (is_array($response) || is_object($response)) {
            $responseObject = (array)$response; // Ubah objek ke array jika perlu
        } else {
            throw new \Exception('Unexpected response type');
        }
        // var_dump($responseObject);
        // Ambil responseCode
        $responseCode = $responseObject['responseCode'];

        // Atur status HTTP berdasarkan tiga angka pertama
        $statusCode = substr($responseCode, 0, 3);
        $this->response->setStatusCode((int)$statusCode); // Set status HTTP
        return $this->response->setJSON($responseObject);

        
    }
## 3. Other Operation

### A. Check Transaction Status

  ```php
   public function debitStatus()
    {
        $requestData = $this->request->getJSON(true);
        $originalPartnerReferenceNo =  $requestData['originalPartnerReferenceNo'] ?? '';
        $originalReferenceNo =  $requestData['originalReferenceNo'] ?? '';
        $originalExternalId =  $requestData['originalExternalId'] ?? '';
        $serviceCode =  $requestData['serviceCode'] ?? '';
        $transactionDate =  $requestData['transactionDate'] ?? '';
        $amountValue = $requestData['amount']['value'] ?? '';
        $amountCurrency = $requestData['amount']['currency'] ?? '';
        $amount = new TotalAmount($amountValue, $amountCurrency);
        
        $merchantId =  $requestData['merchantId'] ?? '';
        $subMerchantId =  $requestData['subMerchantId'] ?? '';
        $externalStoreId =  $requestData['externalStoreId'] ?? '';
        $deviceId = $requestData['additionalInfo']['deviceId'] ?? '';
        $channel = $requestData['additionalInfo']['channel'] ?? '';
        $additionalInfo = new CheckStatusAdditionalInfoRequestDto($deviceId, $channel);
        $requestBody = new DirectDebitCheckStatusRequestDto(
            $originalPartnerReferenceNo,
            $originalReferenceNo,
            $originalExternalId,
            $serviceCode,
            $transactionDate,
            $amount,
            $merchantId,
            $subMerchantId,
            $externalStoreId,
            $additionalInfo
        );

        $response = $this->snap->doCheckStatus($requestBody);

        if (is_array($response) || is_object($response)) {
            $responseObject = (array)$response; // Ubah objek ke array jika perlu
        } else {
            throw new \Exception('Unexpected response type');
        }
        $responseCode = $responseObject['responseCode'];
        $statusCode = substr($responseCode, 0, 3);
        $this->response->setStatusCode((int)$statusCode);
        return $this->response->setJSON($responseObject);
        
    }
  ```

### B. Refund

  ```php
  public function refund()
    {
        $requestData = $this->request->getJSON(true);
        $additionalInfo = new RefundAdditionalInfoRequestDto(
            $requestData['additionalInfo']['channel']
        );
        $originalPartnerReferenceNo =  $requestData['originalPartnerReferenceNo'] ?? '';
        $originalExternalId =  $requestData['originalExternalId'] ?? '';
        $refundAmount = new TotalAmount(
            $requestData['refundAmount']['value'],
            $requestData['refundAmount']['currency']
        );
        $reason =  $requestData['reason'] ?? '';
        $partnerRefundNo =  $requestData['partnerRefundNo'] ?? '';
        $ipAddress = $this->request->getHeaderLine('X-IP-ADDRESS');
        $authCode = $requestData['authCode'];
        $deviceId = $this->request->getHeaderLine('deviceId');
        $requestBody = new RefundRequestDto(
            $additionalInfo,
            $originalPartnerReferenceNo,
            $originalExternalId,
            $refundAmount,
            $reason,
            $partnerRefundNo
        );
        $response = $this->snap->doRefund($requestBody, $authCode, $ipAddress, $deviceId);

        if (is_array($response) || is_object($response)) {
            $responseObject = (array)$response; // Ubah objek ke array jika perlu
        } else {
            throw new \Exception('Unexpected response type');
        }
        $responseCode = $responseObject['responseCode'];
        $statusCode = substr($responseCode, 0, 3);
        $this->response->setStatusCode((int)$statusCode); 
        return $this->response->setJSON($responseObject);

    }
  ```

### C. Balance Inquiry

  ```php
     public function checkBalance()
    {
        $requestData = $this->request->getJSON(true);

        $additionalInfo = new BalanceInquiryAdditionalInfoRequestDto(
            $requestData['additionalInfo']['channel']
        );
        $requestBody = new BalanceInquiryRequestDto(
            $additionalInfo
        );
        $ipAddress = $this->request->getHeaderLine('X-IP-ADDRESS');
        $authCode = $requestData['authCode'];
        $response = $this->snap->doBalanceInquiry($requestBody, $authCode, $ipAddress);

        if (is_array($response) || is_object($response)) {
            $responseObject = (array)$response; // Ubah objek ke array jika perlu
        } else {
            throw new \Exception('Unexpected response type');
        }
        $responseCode = $responseObject['responseCode'];
        $statusCode = substr($responseCode, 0, 3);
        $this->response->setStatusCode((int)$statusCode); 
        return $this->response->setJSON($responseObject);
    }
  ```

## 4. Error Handling and Troubleshooting

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

This section provides common errors and solutions:

| Error Code | Description                           | Solution                                     |
|------------|---------------------------------------|----------------------------------------------|
| `4010000`  | Unauthorized                          | Check if Client ID and Secret Key are valid. |
| `4012400`  | Virtual Account Not Found             | Verify the virtual account number provided.  |
| `2002400`  | Successful                            | Transaction completed successfully.          |


## 5. Appendix

### Glossary
- **VA**: Virtual Account, a temporary payment identifier.
- **MGPC**: Merchant-Generated Payment Code.

### FAQ
- **How do I set up a virtual account?**
  See [Payment Flow and Virtual Account Setup](#payment-flow-and-virtual-account-setup).
