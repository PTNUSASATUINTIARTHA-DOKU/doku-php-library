<?php

require __DIR__ . '/vendor/autoload.php';

use Doku\Snap\Snap;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\VA\Request\UpdateVaRequestDto;
use Doku\Snap\Models\VA\Request\DeleteVaRequestDto;
use Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto;
use Doku\Snap\Models\VA\Request\CreateVARequestDtoV1;
use Doku\Snap\Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\AdditionalInfo\DeleteVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig;
use Doku\Snap\Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\Payment\PayOptionDetailsDto;
use Doku\Snap\Models\Payment\LineItemsDto;
use Doku\Snap\Models\AccountBinding\AccountBindingRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentAdditionalInfoRequestDto;
use Doku\Snap\Models\AccountBinding\AccountBindingAdditionalInfoRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingAdditionalInfoRequestDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppAdditionalInfoRequestDto;
use Doku\Snap\Models\PaymentJumpApp\UrlParamDto;
use Doku\Snap\Models\AccountBinding\AccountBindingResponseDto;
use Doku\Snap\Models\AccountUnbinding\AccountUnbindingResponseDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Models\AccountBinding\AccountBindingAdditionalInfoResponseDto;

// Authentication and configuration
$privateKey = "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCvuA0S+R8RGEoT
xZYfksdNam3/iNrKzY/RqGbN4Gf0juIN8XnUM8dGv4DVqmXQwRMMeQ3N/Y26pMDJ
1v/i6E5BwWasBAveSk7bmUBQYMURzxrvBbvfRNvIwtYDa+cx39HamfiYYOHq4hZV
S6G2m8SqDEhONxhHQmEP9FPHSOjPQWKSlgxrT3BKI9ESpQofcxKRX3hyfh6MedWT
lZpXUJrI9bd6Azg3Fd5wpfHQlLcKSR8Xr2ErH7dNS4I21DTHR+6qx02Tocv5D30O
DamA6yG9hxnFERLVE+8GnJE52Yjjsm5otGRwjHS4ngSShc/Ak1ZyksaCTFl0xEwT
J1oeESffAgMBAAECggEAHv9fxw4NTe2z+6LqZa113RE+UEqrFgWHLlv/rqe8jua5
t+32KNnteGyF5KtHhLjajGO6bLEi1F8F51U3FKcYTv84BnY8Rb1kBdcWAlffy9F2
Fd40EyHJh7PfHwFk6mZqVZ69vNuyXsX9XJSX9WerHLhH9QxBCykJiE/4i3owH4dF
Cd/7ervsP32ukGY3rs/mdcO8ThAWffF5QyGd/A3NMf8jRCZ3FwYfEPrgaj9IHV2f
UrwgVc7JqQaCJTvvjrm4Epjp+1mca036eoDj40H+ImF9qQ80jZee/vvqRXjfU5Qx
ys/MHD6S2aGEG5N5VnEuHLHvT51ytTpKA+mAY/armQKBgQDrQVtS8dlfyfnPLRHy
p8snF/hpqQQF2k1CDBJTaHfNXG37HlccGzo0vreFapyyeSakCdA3owW7ET8DBiO5
WN2Qgb7Vab/7vEiGltK4YU/62+g4F0LjWPp25wnbVj81XXW95QrWKjytjU/tgO2p
h47qr8C+3HqMPj1pQ5tcKpJXCwKBgQC/Nrkn0kT+u4KOxXix5RkRDxwfdylCvuKc
3EfMHFs4vELi1kOhwXEbVTIsbFpTmsXclofqZvjkhepeu9CM6PN2T852hOaI+1Wo
4v57UTW/nkpyo8FZ09PtBvOau5B6FpQU0uaKWrZ0dX/f0aGbQKUxJnFOq++7e7mi
IBfX1QCm/QKBgHtVWkFT1XgodTSuFji2ywSFxo/uMdO3rMUxevILVLNu/6GlOFnd
1FgOnDvvtpLCfQWGt4hTiQ+XbQdy0ou7EP1PZ/KObD3XadZVf8d2DO4hF89AMqrp
3PU1Dq/UuXKKus2BJHs+zWzXJs4Gx5IXJU/YMB5fjEe14ZAsB2j8UJgdAoGANjuz
MFQ3NXjBgvUHUo2EGo6Kj3IgxcmWRJ9FzeKNDP54ihXzgMF47yOu42KoC+ZuEC6x
xg4Gseo5mzzx3cWEqB3ilUMEj/2ZQhl/zEIwWHTw8Kr5gBzQkv3RwiVIyRf2UCGx
ObSY41cgOb8fcwVW1SXuJT4m9KoW8KDholnLoZECgYEAiNpTvvIGOoP/QT8iGQkk
r4GK50j9BoPSJhiM6k236LSc5+iZRKRVUCFEfyMPx6AY+jD2flfGxUv2iULp92XG
2eE1H6V1gDZ4JJw3s5847z4MNW3dj9nIi2bpFssnmoS5qP2IpmJW0QQmRmJZ8j2j
OrzKGlO90/6sNzIDd2DbRSM=
-----END PRIVATE KEY-----";

$clientId = "BRN-0221-1693209567392";
$isProduction = false;
$issuer = "";
$publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr7gNEvkfERhKE8WWH5LHTWpt/4jays2P0ahmzeBn9I7iDfF51DPHRr+A1apl0METDHkNzf2NuqTAydb/4uhOQcFmrAQL3kpO25lAUGDFEc8a7wW730TbyMLWA2vnMd/R2pn4mGDh6uIWVUuhtpvEqgxITjcYR0JhD/RTx0joz0FikpYMa09wSiPREqUKH3MSkV94cn4ejHnVk5WaV1CayPW3egM4NxXecKXx0JS3CkkfF69hKx+3TUuCNtQ0x0fuqsdNk6HL+Q99Dg2pgOshvYcZxRES1RPvBpyROdmI47JuaLRkcIx0uJ4EkoXPwJNWcpLGgkxZdMRMEydaHhEn3wIDAQAB
-----END PUBLIC KEY-----";
$secretKey = "SK-tDzY6MSLBWlNXy3qCsUU";

// Initialize Snap
$Snap = new Snap($privateKey, $publicKey, $clientId, $issuer, $isProduction, $secretKey);

function createVA($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Create VA B2B: " . PHP_EOL;
    
    $partner = ' 8129014';
    // $partner = '    1899';
    $customerno = '17223992157';
    $createVaRequestDto = new CreateVaRequestDto(
        $partner,
        $customerno,
        $partner . $customerno, // customerNo
        "T_" . time(),  // virtualAccountName
        "test.bnc." . time() . "@test.com",  // virtualAccountEmail
        "621722399214895",  // virtualAccountPhone
        "INV_CIMB_" . time(),  // trxId
        new TotalAmount("12500.00", "IDR"),  // totalAmount
        new CreateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new CreateVaVirtualAccountConfig(true)),  // additionalInfo
        'C',  // virtualAccountTrxType
        "2024-08-31T09:54:04+07:00"  // expiredDate
    );

    $result = $Snap->createVa($createVaRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function updateVA($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Updating VA B2B: " . PHP_EOL;
    
    $updateVaRequestDto = new UpdateVaRequestDto(
        "    8129014",  // partnerServiceId
        "17223992155",  // customerNo
        "    812901417223992155",  // virtualAccountNo
        "T_" . time(),  // virtualAccountName
        "test.bnc." . time() . "@test.com",  // virtualAccountEmail
        "00000062798",  // virtualAccountPhone
        "INV_CIMB_" . time(),  // trxId
        new TotalAmount("14000.00", "IDR"),  // totalAmount
        new UpdateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", new UpdateVaVirtualAccountConfig("ACTIVE", "10000.00", "15000.00")),  // additionalInfo
        "O",  // virtualAccountTrxType
        "2024-08-02T15:54:04+07:00"  // expiredDate
    );

    $result = $Snap->updateVa($updateVaRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function deleteVA($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Deleting VA B2B: " . PHP_EOL;
    
    $deleteVaRequestDto = new DeleteVaRequestDto(
        "    8129014",  // partnerServiceId
        "17223992155",  // customerNo
        "    812901417223992155",  // virtualAccountNo
        "INV_CIMB_" . time(),  // trxId
        new DeleteVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB")  // additionalInfo
    );

    $result = $Snap->deletePaymentCode($deleteVaRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function checkVA($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Checking Status VA B2B: " . PHP_EOL;
    
    $checkStatusVaRequestDto = new CheckStatusVaRequestDto(
        "    8129014",  // partnerServiceId
        "17223992155",  // customerNo
        "    812901417223992155",  // virtualAccountNo
        null,
        null,
        null
    );

    $result = $Snap->checkStatusVa($checkStatusVaRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function createVAV1($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Creating VA B2B V1: " . PHP_EOL;
    
    $createVaRequestDtoV1 = new CreateVaRequestDtoV1(
        "1899",  // $mallId
        "CHAIN_MERCHANT",  // $chainMerchant
        "12500.00",  // $amount
        "12500.00",  // $purchaseAmount
        "INV_CIMB_" . time(),  // $transIdMerchant
        "VIRTUAL_ACCOUNT",  // $PaymentType
        "",  // $words
        date("Y-m-d H:i:s"),  // $requestDateTime
        "IDR",  // $currency
        "IDR",  // $purchaseCurrency
        "",  // $sessionId
        "T_" . time(),  // $name
        "test.bnc." . time() . "@test.com",  // $email
        "",  // $additionalData
        "",  // $basket
        "",  // $shippingAddress
        "",  // $shippingCity
        "",  // $shippingState
        "",  // $shippingCountry
        "",  // $shippingZipcode
        "VIRTUAL_ACCOUNT_BANK_CIMB",  // $paymentChannel
        "",  // $address
        "",  // $city
        "",  // $state
        "",  // $country
        "",  // $zipcode
        "",  // $homephone
        "00000062798",  // $mobilephone
        "",  // $workphone
        "",  // $birthday
        "    1899",  // $partnerServiceId
        "2024-06-24T15:54:04+07:00"  // $expiredDate
    );

    $result = $Snap->createVaV1($createVaRequestDtoV1, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function testAccountBinding($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Testing Account Binding: " . PHP_EOL;
    
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

    $result = $Snap->doAccountBinding($accountBindingRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function testAccountUnbinding($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Testing Account Unbinding: " . PHP_EOL;

    $additionalInfo = new AccountUnbindingAdditionalInfoRequestDto("Mandiri");

    $accountUnbindingRequestDto = new AccountUnbindingRequestDto(
        "tokenB2b2c123",  // tokenId (tokenB2b2c)
        $additionalInfo
    );

    $result = $Snap->doAccountUnbinding($accountUnbindingRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function testPaymentJumpApp($Snap, $privateKey, $clientId, $secretKey, $isProduction) {
    echo "Testing Payment Jump App: " . PHP_EOL;

    $timestamp = time();
    $totalAmount = new TotalAmount("50000.00", "IDR");

    $lineItems = [
        new LineItemsDto("Product A", "25000.00", "1"),
        new LineItemsDto("Product B", "25000.00", "1")
    ];

    $additionalInfo = new PaymentJumpAppAdditionalInfoRequestDto(
        "Mandiri",  // channel
        "Payment for Order #123",  // remarks
        null
    );

    $urlParam = new UrlParamDto("url", "type", "no");

    $paymentJumpAppRequestDto = new PaymentJumpAppRequestDto(
        "ORDER_" . $timestamp,  // partnerReferenceNo
        date('Y-m-d H:i:s', strtotime('+1 day')),  // validUpTo (24 hours from now)
        "12",  // pointOfInitiation (example value, adjust as needed)
        $urlParam,  // urlParam (null or UrlParamDto instance)
        $totalAmount,
        $additionalInfo
    );

    $result = $Snap->doPaymentJumpApp($paymentJumpAppRequestDto, $privateKey, $clientId, $secretKey, $isProduction);
    echo json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function convertV1toSnap($Snap, $dtov1) {
    echo "Convert V1 to Snap: " . PHP_EOL;
    $virtualAccount = $Snap->createVaV1($dtov1);
    echo json_encode($virtualAccount, JSON_PRETTY_PRINT);
}

function convertVAInquiryRequestSnapToV1Form($Snap, string $snapJson)
{
    echo "Convert VA Inquiry Request Snap To V1 Form: " . PHP_EOL;
    $result = $Snap->convertVAInquiryRequestSnapToV1Form($snapJson);
    echo $result . "\n";
}

function convertVAInquiryResponseV1XmlToSnapJson($Snap, string $xmlString)
{
    echo "Convert VA Inquiry Response V1 Xml To Snap Json: " . PHP_EOL;
    $result = $Snap->convertVAInquiryResponseV1XmlToSnapJson($xmlString);
    echo $result . "\n";
}

function convertDOKUNotificationToForm($Snap, string $notification){
    echo "Convert DOKU Notification To Form: " . PHP_EOL;
    $result = $Snap->convertDOKUNotificationToForm($notification);
    echo $result . "\n";
}



// Example usage
// $result = createVA($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = updateVA($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = deleteVA($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = checkVA($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = createVAV1($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = testAccountBinding($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = testAccountUnbinding($Snap, $privateKey, $clientId, $secretKey, $isProduction);
// $result = testPaymentJumpApp($Snap, $privateKey, $clientId, $secretKey, $isProduction);
$result = convertDOKUNotificationToForm($Snap, $notification);
