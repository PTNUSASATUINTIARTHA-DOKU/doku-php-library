<?php

// To run : ./vendor/bin/phpunit tests/SnapTest.php

namespace Doku\Snap;
use PHPUnit\Framework\TestCase;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppResponseDto;
use Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppAdditionalInfoRequestDto;
use Doku\Snap\Models\PaymentJumpApp\UrlParamDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryRequestDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryResponseDto;
use Doku\Snap\Models\BalanceInquiry\BalanceInquiryAdditionalInfoRequestDto;
use Doku\Snap\Models\Refund\RefundRequestDto;
use Doku\Snap\Models\Refund\RefundResponseDto;
use Doku\Snap\Models\Refund\RefundAdditionalInfoRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusResponseDto;
use Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoRequestDto;
use Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoResponseDto;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Controllers\DirectDebitController;

class SnapTest extends TestCase
{
    private Snap $snap;
    private $tokenController;

    private string $privateKey = "-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC3EFZ9ahjQQLzq
9Nf3d9BbmDaMIEaFSsrgmxUUslWWKcpR3FWNT8anUm1p5imw3AcPDMwnc8P1f6Fy
25eWSSgqdIeKvP7T93b+ShYCkJeGnpPN6VWqfdmECt2cBxgpQtOjjqU1jUYG5sPX
fKCbFdE8D+BESzWHia/+EhpTFv4xmbaFhQtUjqhsMUNUEmr4GQY9UhSFGhHM+MVJ
T3MGJFKxkKLGnx0R7i9gP9eFE+wvFHbREzrgs/D5j1J2/o1oGSBeSzHj5CT3ZPNJ
Hg7aVvRkUptfqZQAfMHuoL804hICXQxYPfSSQnJuC2fpOtrJRedtPuAeDMWN/T7R
REEXsHvRAgMBAAECggEABvS7uJnAth8TnqDtmOVoWSakQfhS/asdIOI7r5TkLCfL
3IbxCETVAVQgPLMmd+YwbXm4wzICqvAt2BGwhaEjgdN6OAMc8rx3PMvfYLsSSub5
KkyYuPj9vCb2i0B4wk6cAJ3BuNJ0q/v2SpideK8gS3Y1+Rpbfxo0AgU5k1kvEP7c
C5KLeJpfJUsYzdROgz4Nm31N1f78b+xpmBk5BunVi/p5V88ffD8E0cXXvgGAOrll
StUZF6TcZRHWZm4uBwKzapDdRUJw4bfAs7KkPu1BjXYb+UQTJtf3QkGdPdkbpIAq
NqUTZHcpid69rs7WRHYbnpLesE5S/EnjsSTvKkqTAQKBgQDemRGgu3nGmTkItl/5
8RMe8EgKWrZ5bbrmifCV9sbBzSvCG/x67lmYH1nEQgb7P+cshSxExNx8oNMBKjo5
SbYD8t+brHrbAKpeMspIKituorSgKyAI78qmerDwETdVLjaptAeB2rtItDflKLhi
vvc2oMENrs9P6vwRaQBRL46lEQKBgQDSiJigOBrlZ7ktIYcgjhItj5ym2tBQZR3l
r3se8QCJBX5qkhHBr8ZW+RNbhVUIJOc6zS+FVkNXDjTujshXmCg2rvunRMeEY8lr
bqY6b1qU7Dy2ayUxOcCrMncyu0m+816sKea439IAbBsOBDpLfy3c9XVxRQNl/9jA
vkVlW/1qwQKBgEtG8oorvGPoHzyOCGkLGM9GrOYrhTgNXr5l+aGNYevaSakMM9cS
0eO6/m17csb29mO86ZqcBIB1FsZ3FFeZUN+G/A22R5nWYMcYYAYFlMiGZ5Ue4GeF
SEACj+GvwMmipkO/qSZF5T4SDDEIE0r5j7q+pGrPKja5neL5Ym0SKygxAoGAT0/Y
N5uLPlrp3r+fuTrsTC+q8wzBp1fAgJOwDUL7UaM5MYqfl9jRHlis2zCKjQvh9Dvy
KAgMTFJ0zF4LyTvwAlG/Sg9WHmC9M3S2uJLUi//HC22n0DiShav4TatSp9XlF3Sd
j435/eC7/HbJQ0HBFFmACdDW6+kLTekgGYQjHsECgYBMiwrH5sAV9bWa8M4cTZFj
rftc352gjDhdxFsUmU6oDBa5ErMaVqe+r/RiIMWO3a3PBoYyYqMRF5JB/pF+ZN7D
bEjWwooi232C/6fIDRnQtJs/huZm1msK410lJ8dsCG/5H+nuFpPiGQgHCwXiwSw+
ZMjguO6OOHnN77tklXQqCg==
-----END PRIVATE KEY-----";
private $issuer = "doku";
private $clientId = "BRN-0208-1720408264694";
private $publicKey = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtxBWfWoY0EC86vTX93fQ
W5g2jCBGhUrK4JsVFLJVlinKUdxVjU/Gp1JtaeYpsNwHDwzMJ3PD9X+hctuXlkko
KnSHirz+0/d2/koWApCXhp6TzelVqn3ZhArdnAcYKULTo46lNY1GBubD13ygmxXR
PA/gREs1h4mv/hIaUxb+MZm2hYULVI6obDFDVBJq+BkGPVIUhRoRzPjFSU9zBiRS
sZCixp8dEe4vYD/XhRPsLxR20RM64LPw+Y9Sdv6NaBkgXksx4+Qk92TzSR4O2lb0
ZFKbX6mUAHzB7qC/NOISAl0MWD30kkJybgtn6TrayUXnbT7gHgzFjf0+0URBF7B7
0QIDAQAB
-----END PUBLIC KEY-----";
    private $dokuPublicKey = "-----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwQY+iVi/dwZjm/2fXD+xmopuldumL8aBWPvkhqdy2Lcz7Fd2bwoLoW7xGRYzDLs2jC+CDwILynrnZlZQ+LBg8mzflqWPRiiHVf0VJrHmYGeS9rOY/R7V/dODE8QlxmkHZ52KGbZdphXKcCCP178+AdQyo7+8UXJUvlJ9VkYyWREDQ5q5XB/9cXxeD1MgwXcgZIVZ+2ZluHzX9bl6B7bJ8n8OXkUYsExfl3ixtz895tTq5P0eXSVVwg6Yb9LXXS7lBHkgmETgKtT4N2Cy3C1U2cfyRcNUtMXx7L84ecAVQMxfy122L9VE3I8eAckYY7vgvS1LpMyg4tVaDX108TdfVwIDAQAB
    -----END PUBLIC KEY-----";
    private $secretKey = 'SK-VknOxwR4xZSEPnG7fpJo';
    private $isProduction = "false";

    protected function setUp(): void
    {
        $this->tokenController = $this->createMock(Controllers\TokenController::class);
        $this->directDebitController = $this->createMock(DirectDebitController::class);
        $this->timestamp = time();
        $this->snap = new Snap($this->privateKey, $this->publicKey, $this->dokuPublicKey, $this->clientId, $this->issuer, $this->isProduction, $this->secretKey);
    }

    private function getTokenB2BResponseDto(string $responseCode): Models\Token\TokenB2BResponseDto
    {
        $response = new Models\Token\TokenB2BResponseDto(
            $responseCode,
            "",
            "",
            "",
            900,
            ""
        );
        return $response;
    }
    
    private function getCreateVaRequestDto($virtualAccountChannel = 'VIRTUAL_ACCOUNT_BANK_MANDIRI')
    {
        $timestamp = time();
        $partner = ' 8129014';
        $virtualno = '1722399214993';
        $request = new Models\VA\Request\CreateVaRequestDto(
                $partner,
                $virtualno,
                $partner . $virtualno,
                // null,null,null,
            "T_" . $timestamp, // $virtualAccountName
            "test.bnc." . $timestamp . "@test.com", // $virtualAccountEmail
            "621722399214895", // $virtualAccountPhone
            "INV_CIMB_" . $timestamp, // $trxId
            new Models\TotalAmount\TotalAmount("12500.00", "IDR"), // $totalAmount
            new Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_CIMB", 
            new Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig(true)), // $additionalInfo
            'C', // $virtualAccountTrxType
            "2024-08-01T09:54:04+07:00" // $expiredDate
            );
        return $request;
    }
    private function getUpdateVaRequestDto()
    {
        $request = new Models\VA\Request\UpdateVaRequestDto(
            '  888994',
            '00000000000000000001',
            '  88899400000000000000000001',
            'Test User',
            'test@example.com',
            '628123456789',
            '23219829713',
            new Models\TotalAmount\TotalAmount('1000', 'IDR'),
            new Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo(
                'VIRTUAL_ACCOUNT_BANK_MANDIRI', 
                new Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig('ACTIVE')),
            'C',
            "2024-08-24T15:54:04+07:00"
        );
        // $request->partnerServiceId = ' 888994';
        // $request->customerNo = '00000000000000000001';
        // $request->virtualAccountNo = ' 88899400000000000000000001';
        // $request->virtualAccountName = 'Test User';
        // $request->virtualAccountEmail = 'test@example.com';
        // $request->virtualAccountPhone = '628123456789';
        // $request->trxId = '23219829713';
        //$request->totalAmount = new Models\TotalAmount\TotalAmount('1000', 'IDR');
        //$request->additionalInfo = new Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo('VIRTUAL_ACCOUNT_BANK_MANDIRI', new Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig('ACTIVE'));
        //$request->additionalInfo->channel = 'VIRTUAL_ACCOUNT_MANDIRI';
        //$request->additionalInfo->virtualAccountConfig = new Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig('ACTIVE');
        //$request->additionalInfo->virtualAccountConfig->status = 'ACTIVE';
        // $request->virtualAccountTrxType = 'C';
        // $request->expiredDate = '2023-01-01T10:55:00+07:00';
        
        return $request;
    }
    private function getDeleteVaRequestDto(): Models\VA\Request\DeleteVaRequestDto
    {
        $request = new Models\VA\Request\DeleteVaRequestDto(
            "  888994",
            "00000000000000000001",
            "  88899400000000000000000001",
            "validTrxId",
            new Models\VA\AdditionalInfo\DeleteVaRequestAdditionalInfo("VIRTUAL_ACCOUNT_BANK_MANDIRI")
        );
        // $request->partnerServiceId = " 888994";
        // $request->customerNo = "00000000000000000001";
        // $request->virtualAccountNo = " 88899400000000000000000001";
        // $request->trxId = "validTrxId";
        //$request->additionalInfo = new Models\VA\AdditionalInfo\DeleteVaRequestAdditionalInfo("validChannel");
        // $request->additionalInfo->channel = "validChannel";
        return $request;
    }

    private function getCheckStatusVaRequestDto(): Models\VA\Request\CheckStatusVaRequestDto
    {
        // Implement this method to return a populated CheckStatusVaRequestDto
        return new Models\VA\Request\CheckStatusVaRequestDto(
            "    1899",
            "000000000461",
            "    1899000000000461",
            null,
            null,
            null
        );
    }

    public function testGetB2bToken_Success(): void
    {
        $this->tokenController
            ->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("2007300"));
        $response = $this->snap->getB2BToken();
        $this->assertEquals("2007300", $response->responseCode);
    }

    public function testGetB2bToken_ClientIdInvalid(): void
    {
       $this->clientId = "12323";
    
       $response = $this->snap->getB2BToken();
       $this->assertEquals("2007300", $response->responseCode);
    }
    

    public function testCreateVa_Success(): void
    {
        $request = $this->getCreateVaRequestDto();
        $response = $this->snap->createVa($request);
        
        $this->assertNotEmpty($response->responseCode);
    }

    public function testCreateVa_PartnerIdNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->partnerServiceId = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_PartnerIdLengthIsNot8Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->partnerServiceId = '123456789';
        $this->snap->createVa($request);
    }

    public function testCreateVa_PartnerIdLengthIsNotNumerical(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must consist of up to 7 spaces followed by 1 to 8 digits. Make sure partnerServiceId follows this format. Example: ' 888994' (2 spaces and 6 digits).");
        
        $request = $this->getCreateVaRequestDto();
        $request->partnerServiceId = '1234567z';
        $this->snap->createVa($request);
    }

    public function testCreateVa_CustomerNoIsMoreThan20(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->customerNo = '123456789012345678901';
        $this->snap->createVa($request);
    }

    public function testCreateVa_CustomerNoIsNotNumerical(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->customerNo = '123456789z';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountNoIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountNo = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountNoIsNotValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountNo = '    189920240704002';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountNameIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountName cannot be null. Please provide a virtualAccountName. Example: 'Toru Yamashita'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountName = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountNameIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountName must be between 1 and 255 characters long. Ensure that virtualAccountName is not empty and no longer than 255 characters. Example: 'Toru Yamashita'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountName = '';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountNameIsMoreThan255(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountName must be between 1 and 255 characters long. Ensure that virtualAccountName is not empty and no longer than 255 characters. Example: 'Toru Yamashita'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountName = str_repeat('a', 256);
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountEmailIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountEmail must be between 1 and 255 characters long. Ensure that virtualAccountEmail is not empty and no longer than 255 characters. Example: 'toru@example.com'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountEmail = '';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountEmailIsMoreThan255(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountEmail must be between 1 and 255 characters long. Ensure that virtualAccountEmail is not empty and no longer than 255 characters. Example: 'toru@example.com'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountEmail = str_repeat('a', 246) . '@email.com';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountEmailIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountEmail must be a valid email address. Example: 'toru@example.com'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountEmail = 'sdk@emailcom';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountPhoneIsLessThan9(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountPhone = '12345678';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountPhoneIsMoreThan30(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountPhone = '1234567890123456789012345678901';
        $this->snap->createVa($request);
    }

    public function testCreateVa_TrxIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->trxId = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_TrxIdIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->trxId = '';
        $this->snap->createVa($request);
    }

    public function testCreateVa_TrxIdIsMoreThan64(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->trxId = str_repeat('a', 65);
        $this->snap->createVa($request);
    }

    public function testCreateVa_ValueIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value cannot be null.");
        
        $request = $this->getCreateVaRequestDto();
        $request->totalAmount->value = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_ValueIsLessThan4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value must be at least 4 characters long and formatted as 0.00. Ensure that totalAmount.value is at least 4 characters long and in the correct format. Example: '100.00'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->totalAmount->value = '100';
        $this->snap->createVa($request);
    }

    public function testCreateVa_ValueIsMoreThan19(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value must be 19 characters or fewer and formatted as 9999999999999999.99. Ensure that totalAmount.value is no longer than 19 characters and in the correct format. Example: '9999999999999999.99'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->totalAmount->value = '12345678901234567890';
        $this->snap->createVa($request);
    }

    public function testCreateVa_CurrencyIsNot3Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->totalAmount->currency = 'ID';
        $this->snap->createVa($request);
    }

    public function testCreateVa_CurrencyIsNotIDR(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->totalAmount->currency = 'USD';
        $this->snap->createVa($request);
    }

    public function testCreateVa_ChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel cannot be null.");
        
        $request = $this->getCreateVaRequestDto();
        $request->additionalInfo->channel = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_ChannelIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel must be at least 1 character long. Ensure that additionalInfo.channel is not empty. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->additionalInfo->channel = '';
        $this->snap->createVa($request);
    }

    public function testCreateVa_ChannelIsMoreThan30(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel must be 30 characters or fewer. Ensure that additionalInfo.channel is no longer than 30 characters. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->additionalInfo->channel = 'VIRTUAL_ACCOUNT_BANK_MANDIRI_TEST';
        $this->snap->createVa($request);
    }

    public function testCreateVa_ChannelIsNotValidChannel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel is not valid. Ensure that additionalInfo.channel is one of the valid channels. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->additionalInfo->channel = '5Vl3mjMJpA6NuUNHWrucSymfjlWPCb';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountTrxIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType cannot be null.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountTrxType = null;
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountTrxIsNot1Digit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType must be exactly 1 character long. Ensure that virtualAccountTrxType is either 'C', 'O', or 'V'. Example: 'C'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountTrxType = 'CC';
        $this->snap->createVa($request);
    }

    public function testCreateVa_VirtualAccountTrxIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType must be either 'C', 'O', or 'V'. Ensure that virtualAccountTrxType is one of these values. Example: 'C'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->virtualAccountTrxType = 'A';
        $this->snap->createVa($request);
    }

    public function testCreateVa_ExpiredDateIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("expiredDate must be in ISO-8601 format. Ensure that expiredDate follows the correct format. Example: '2023-01-01T10:55:00+07:00'.");
        
        $request = $this->getCreateVaRequestDto();
        $request->expiredDate = '2024-07-11';
        $this->snap->createVa($request);
    }

    public function testUpdateVa_Success(): void
    {
        $request = $this->getUpdateVaRequestDto();
        $response = $this->snap->updateVa($request);
        
        $this->assertNotEmpty($response->responseCode);
    }


    public function testUpdateVa_PartnerServiceIdNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->partnerServiceId = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_PartnerServiceIdIsNot8Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->partnerServiceId = '123456789';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_PartnerServiceIdIsNotNumerical(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must consist of up to 7 spaces followed by 1 to 8 digits. Make sure partnerServiceId follows this format. Example: ' 888994' (2 spaces and 6 digits).");
        
        $request = $this->getUpdateVaRequestDto();
        $request->partnerServiceId = '1234567z';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_CustomerNoIsMoreThan20(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->customerNo = '123456789012345678901';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_CustomerNoIsNotNumerical(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->customerNo = '123456789z';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountNoIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountNo = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountNoIsNotValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountNo = '    1899000000000651';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountNameIsMoreThan255(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountName must be between 1 and 255 characters long. Ensure that virtualAccountName is not empty and no longer than 255 characters. Example: 'Toru Yamashita'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountName = str_repeat('a', 256);
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountEmailIsMoreThan255(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountEmail must be between 1 and 255 characters long. Ensure that virtualAccountEmail is not empty and no longer than 255 characters. Example: 'toru@example.com'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountEmail = str_repeat('a', 246) . '@email.com';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountEmailIsInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountEmail must be a valid email address. Example: 'toru@example.com'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountEmail = 'sdk@emailcom';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountPhoneIsLessThan9(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountPhone = '12345678';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountPhoneIsMoreThan30(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountPhone must be between 9 and 30 characters long. Ensure that virtualAccountPhone is at least 9 characters long and no longer than 30 characters. Example: '628123456789'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountPhone = '1234567890123456789012345678901';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_TrxIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->trxId = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_TrxIdIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->trxId = '';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_TrxIdIsMoreThan64(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be between 1 and 64 characters long. Ensure that trxId is not empty and no longer than 64 characters. Example: '23219829713'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->trxId = str_repeat('a', 65);
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ValueIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value cannot be null.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->totalAmount->value = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ValueIsLessThan4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value must be at least 4 characters long and formatted as 0.00. Ensure that totalAmount.value is at least 4 characters long and in the correct format. Example: '100.00'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->totalAmount->value = '100';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ValueIsMoreThan19(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.value must be 19 characters or fewer and formatted as 9999999999999999.99. Ensure that totalAmount.value is no longer than 19 characters and in the correct format. Example: '9999999999999999.99'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->totalAmount->value = '12345678901234567890';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_CurrencyIsNot3Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->totalAmount->currency = 'ID';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_CurrencyIsNotIDR(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("totalAmount.currency must be 'IDR'. Ensure that totalAmount.currency is 'IDR'. Example: 'IDR'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->totalAmount->currency = 'USD';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel cannot be null.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->channel = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ChannelIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel must be at least 1 character long. Ensure that additionalInfo.channel is not empty. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->channel = '';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ChannelIsMoreThan30(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel must be 30 characters or fewer. Ensure that additionalInfo.channel is no longer than 30 characters. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->channel = 'VIRTUAL_ACCOUNT_BANK_MANDIRI_TEST';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ChannelIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel is not valid. Ensure that additionalInfo.channel is one of the valid channels. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->channel = 'VIRTUAL_ACCOUNT_BANK';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_StatusIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("status must be either 'ACTIVE' or 'INACTIVE'. Ensure that status is one of these values. Example: 'INACTIVE'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->status = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_StatusIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("status must be either 'ACTIVE' or 'INACTIVE'. Ensure that status is one of these values. Example: 'INACTIVE'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->status = '';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_StatusIsMoreThan20(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("status must be either 'ACTIVE' or 'INACTIVE'. Ensure that status is one of these values. Example: 'INACTIVE'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->status = str_repeat('a', 21);
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_StatusIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("status must be either 'ACTIVE' or 'INACTIVE'. Ensure that status is one of these values. Example: 'INACTIVE'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->status = 'CLOSED';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountTrxTypeIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType cannot be null.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountTrxType = null;
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountTrxTypeIsNot1Digit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType must be exactly 1 character long. Ensure that virtualAccountTrxType is either 'C', 'O', or 'V'. Example: 'C'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountTrxType = '12';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_VirtualAccountTrxTypeIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountTrxType must be either 'C', 'O', or 'V'. Ensure that virtualAccountTrxType is one of these values. Example: 'C'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->virtualAccountTrxType = 'A';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_ExpiredDateIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("expiredDate must be in ISO-8601 format. Ensure that expiredDate follows the correct format. Example: '2023-01-01T10:55:00+07:00'.");
        
        $request = $this->getUpdateVaRequestDto();
        $request->expiredDate = '2024-07-11';
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_MinAmountGreaterThanMaxAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("maxAmount cannot be lesser than minAmount");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->minAmount = '1000000';
        $request->additionalInfo->virtualAccountConfig->maxAmount = '500000';
        $request->virtualAccountTrxType = 'O'; // or 'V'
        $this->snap->updateVa($request);
    }

    public function testUpdateVa_MinMaxAmountNotSupportedForClosedVA(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Only supported for virtualAccountTrxType O and V only");
        
        $request = $this->getUpdateVaRequestDto();
        $request->additionalInfo->virtualAccountConfig->minAmount = '500000';
        $request->additionalInfo->virtualAccountConfig->maxAmount = '1000000';
        $request->virtualAccountTrxType = 'C';
        $this->snap->updateVa($request);
    }

    public function testDeletePaymentCode_Success(): void
    {
        $request = $this->getDeleteVaRequestDto();
        $response = $this->snap->deletePaymentCode($request);
        $this->assertNotEmpty($response->responseCode);
    }

    public function testDeletePaymentCode_PartnerServiceIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        $request = $this->getDeleteVaRequestDto();
        $request->partnerServiceId = null;
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_PartnerServiceIdIsNot8Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        $request = $this->getDeleteVaRequestDto();
        $request->partnerServiceId = "123456789";
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_CustomerNoIsMoreThan20(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        $request = $this->getDeleteVaRequestDto();
        $request->customerNo = "123456789012345678901";
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_CustomerNoIsNotNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        $request = $this->getDeleteVaRequestDto();
        $request->customerNo = "1234567z";
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_VirtualAccountNoIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        $request = $this->getDeleteVaRequestDto();
        $request->virtualAccountNo = null;
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_VirtualAccountNoIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo must be the concatenation of partnerServiceId and customerNo. Example: ' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        $request = $this->getDeleteVaRequestDto();
        $request->virtualAccountNo = "    189920240704000";
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_TrxIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId cannot be null. Please provide a trxId. Example: '23219829713'.");
        $request = $this->getDeleteVaRequestDto();
        $request->trxId = null;
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_TrxIdIsLessThan1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be at least 1 character long. Ensure that trxId is not empty. Example: '23219829713'.");
        $request = $this->getDeleteVaRequestDto();
        $request->trxId = "";
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_TrxIdIsMoreThan64(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("trxId must be 64 characters or fewer. Ensure that trxId is no longer than 64 characters. Example: '23219829713'.");
        $request = $this->getDeleteVaRequestDto();
        $request->trxId = str_repeat("a", 65);
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_AdditionalInfoIsInvalid(): void
    {
        $this->expectException(\TypeError::class);
        $request = $this->getDeleteVaRequestDto();
        $request->additionalInfo = new \stdClass();
        $this->snap->deletePaymentCode($request);
    }

    public function testDeletePaymentCode_ChannelIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("additionalInfo.channel is not valid. Ensure that additionalInfo.channel is one of the valid channels. Example: 'VIRTUAL_ACCOUNT_MANDIRI'.");
        $request = $this->getDeleteVaRequestDto();
        $request->additionalInfo->channel = "INVALID_CHANNEL";
        $this->snap->deletePaymentCode($request);
    }



    public function testCheckStatusVa_Success(): void
    {
        $request = $this->getCheckStatusVaRequestDto();
        $response = $this->snap->checkStatusVa($request);
        $this->assertNotEmpty($response->responseCode);
    }

    public function testCheckStatusVa_PartnerServiceIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId cannot be null. Please provide a partnerServiceId. Example: ' 888994'.");
        $request = $this->getCheckStatusVaRequestDto();
        $request->partnerServiceId = null;
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_PartnerServiceIdIsNot8Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must be exactly 8 characters long. Ensure that partnerServiceId has 8 characters, left-padded with spaces. Example: ' 888994'.");
        $request = $this->getCheckStatusVaRequestDto();
        $request->partnerServiceId = "1234567";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_PartnerServiceIdIsNotNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("partnerServiceId must consist of up to 7 spaces followed by 1 to 8 digits. Make sure partnerServiceId follows this format. Example: ' 888994' (2 spaces and 6 digits).");
        $request = $this->getCheckStatusVaRequestDto();
        $request->partnerServiceId = "1234567z";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_CustomerNoIsMoreThan20(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must be 20 characters or fewer. Ensure that customerNo is no longer than 20 characters. Example: '00000000000000000001'.");
        $request = $this->getCheckStatusVaRequestDto();
        $request->customerNo = "123456789012345678901";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_CustomerNoIsNotNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("customerNo must consist of only digits. Ensure that customerNo contains only numbers. Example: '00000000000000000001'.");
        $request = $this->getCheckStatusVaRequestDto();
        $request->customerNo = "1234567z";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_VirtualAccountNoIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("virtualAccountNo cannot be null. Please provide a virtualAccountNo. Example: ' 88899400000000000000000001'.");
        $request = $this->getCheckStatusVaRequestDto();
        $request->virtualAccountNo = null;
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_VirtualAccountNoIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("' 88899400000000000000000001' (where partnerServiceId is ' 888994' and customerNo is '00000000000000000001').");
        $request = $this->getCheckStatusVaRequestDto();
        $request->virtualAccountNo = "    1899000000000660";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_InquiryRequestIdIsMoreThan128(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('inquiryRequestId must be 128 characters or fewer.');
        $request = $this->getCheckStatusVaRequestDto();
        $request->inquiryRequestId = "CIwxu2v0XgURbX2RYclSfsw4N6fd29YIgvgv1LJpkmSPItG7jrC8ARlKyRhfkgiVnSJvKWRBAu8u0wPyGg0N8mWA8vcSCEvcYsVWut7NNctBkNLT6Le2rBRiEMchWfv4z";
        $this->snap->checkStatusVa($request);
    }

    public function testCheckStatusVa_PaymentRequestIdIsMoreThan128(): void
    {        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('paymentRequestId must be 128 characters or fewer.');
        $request = $this->getCheckStatusVaRequestDto();
        $request->paymentRequestId = "CI wxu2v0XgURbX2RYclSfsw4N6fd29YIgvgv1LJpkmSPItG7jrC8ARlKyRhfkgiVnSJvKWRBAu8u0wPyGg0N8mWA8vcSCEvcYsVWut7NNctBkNLT6Le2rBRiEMchWfv4z";
        $this->snap->checkStatusVa($request);
    }

    public function testDirectDebitPaymentJumpApp_Success(): void
    {
        $this->directDebitController->method('doPaymentJumpApp')
            ->willReturn($this->getPaymentJumpAppResponseDto("2005400"));
        $response = $this->snap->doPaymentJumpApp(
            $this->getPaymentJumpAppRequestDto(),
            "deviceId",
            $this->privateKey,
            $this->clientId,
            $this->secretKey,
            false
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitPaymentJumpApp_Failed(): void
    {
        $request = $this->getPaymentJumpAppRequestDto();

        $response = $this->snap->doPaymentJumpApp(
            $request,
            "deviceId",
             $this->privateKey,
            $this->clientId,
            $this->secretKey,
            false
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitBalanceInquiry_Success(): void
    {
        $this->directDebitController->method('doBalanceInquiry')
            ->willReturn($this->getBalanceInquiryResponseDto("2001100"));

        $response = $this->snap->doBalanceInquiry(
            $this->getBalanceInquiryRequestDto(),
            "authCode",
            "ipAddress"
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitBalanceInquiry_Failed(): void
    {
        $request = $this->getBalanceInquiryRequestDto();

        $response = $this->snap->doBalanceInquiry($request, "authCode","ipAdress");

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitRefund_Success(): void
    {
        $this->directDebitController->method('doRefund')
            ->willReturn($this->getRefundResponseDto("2005800"));

        $response = $this->snap->doRefund(
            $this->getRefundRequestDto(),
            "authCode",
            "ipAddress",
            "deviceId"
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitRefund_Failed(): void
    {
        $request = $this->getRefundRequestDto();
        $request->additionalInfo->channel = null;

        $response = $this->snap->doRefund(
            $this->getRefundRequestDto(),
            "authCode",
            "ipAddress",
            "deviceId"
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitCheckStatus_Success(): void
    {
        $this->directDebitController->method('doCheckStatus')
            ->willReturn($this->getCheckStatusResponseDto("2005500"));

        $response = $this->snap->doCheckStatus(
            $this->getCheckStatusRequestDto(),
            "authCode",
             $this->privateKey,
            $this->clientId,
            $this->secretKey,
            false
        );

        $this->assertNotEmpty($response->responseCode);
    }

    public function testDirectDebitCheckStatus_Failed(): void
    {
        $request = $this->getCheckStatusRequestDto();

        $response = $this->snap->doCheckStatus(
            $request,
            "authCode",
             $this->privateKey,
            $this->clientId,
            $this->secretKey,
            false
        );

        $this->assertEquals("5005501", $response->responseCode);
    }

    private function getPaymentJumpAppRequestDto(): PaymentJumpAppRequestDto
    {
        return new PaymentJumpAppRequestDto(
            "ORDER_" . time(),
            date('Y-m-d\TH:i:sP', strtotime('+1 day')),
            "12",
            new UrlParamDto("https://example.com", "PAY_RETURN", "N"),
            new TotalAmount("50000.00", "IDR"),
            new PaymentJumpAppAdditionalInfoRequestDto("EMONEY_SHOPEE_PAY_SNAP", null, null)
        );
    }

    private function getPaymentJumpAppResponseDto(string $responseCode): PaymentJumpAppResponseDto
    {
        return new PaymentJumpAppResponseDto($responseCode, "message", "http://example.com", "REF123");
    }

    private function getBalanceInquiryRequestDto(): BalanceInquiryRequestDto
    {
        return new BalanceInquiryRequestDto(
            new BalanceInquiryAdditionalInfoRequestDto("DIRECT_DEBIT_MANDIRI")
        );
    }

    private function getBalanceInquiryResponseDto(string $responseCode): BalanceInquiryResponseDto
    {
        return new BalanceInquiryResponseDto($responseCode, "message", []);
    }

    private function getRefundRequestDto(): RefundRequestDto
    {
        return new RefundRequestDto(
            new RefundAdditionalInfoRequestDto("EMONEY_OVO_SNAP"),
            "ORIG123",
            "EXT456",
            new TotalAmount("100.00", "IDR"),
            "Customer request",
            "REF789"
        );
    }

    private function getRefundResponseDto(string $responseCode): RefundResponseDto
    {
        return new RefundResponseDto(
            $responseCode,
            "message",
            new TotalAmount("100.00", "IDR"),
            "ORIG123",
            "REF456",
            "REFUND789",
            "PARTNER_REF123",
            "2023-01-01T12:00:00+07:00"
        );
    }

    private function getCheckStatusRequestDto(): CheckStatusRequestDto
    {
        return new CheckStatusRequestDto(
            "ORIG123",
            "REF456",
            "EXT789",
            "SERVICE001",
            date('Y-m-d\TH:i:sP'),
            new TotalAmount("100000.00", "IDR"),
            "MERCHANT001",
            "SUBMERCHANT001",
            "STORE001",
            new CheckStatusAdditionalInfoRequestDto("DEVICE001", "DIRECT_DEBIT_MANDIRI")
        );
    }

    private function getCheckStatusResponseDto(string $responseCode): CheckStatusResponseDto
    {
        return new CheckStatusResponseDto(
            $responseCode,
            "message",
            "ORIG123",
            "REF456",
            "APPROVAL789",
            "EXT123",
            "SERVICE001",
            "COMPLETED",
            "Transaction completed",
            "0000",
            "Success",
            "SESSION123",
            "REQ123",
            [],
            new TotalAmount("100.00", "IDR"),
            new TotalAmount("10.00", "IDR"),
            "2023-01-01T12:00:00+07:00",
            new CheckStatusAdditionalInfoResponseDto("DEVICE123", "CHANNEL001")
        );
    }
}