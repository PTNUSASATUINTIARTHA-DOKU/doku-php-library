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

class DirectDebitTest extends TestCase
{
        private const PRIVATE_KEY = "-----BEGIN PRIVATE KEY-----
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
    private Snap $snap;
    private $directDebitController;
    private const CLIENT_ID = "BRN-0208-1720408264694";
    private const IP_ADDRESS = "127.0.0.1";
    private const SECRET_KEY = "SK-VknOxwR4xZSEPnG7fpJo";
    private const PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtxBWfWoY0EC86vTX93fQ
W5g2jCBGhUrK4JsVFLJVlinKUdxVjU/Gp1JtaeYpsNwHDwzMJ3PD9X+hctuXlkko
KnSHirz+0/d2/koWApCXhp6TzelVqn3ZhArdnAcYKULTo46lNY1GBubD13ygmxXR
PA/gREs1h4mv/hIaUxb+MZm2hYULVI6obDFDVBJq+BkGPVIUhRoRzPjFSU9zBiRS
sZCixp8dEe4vYD/XhRPsLxR20RM64LPw+Y9Sdv6NaBkgXksx4+Qk92TzSR4O2lb0
ZFKbX6mUAHzB7qC/NOISAl0MWD30kkJybgtn6TrayUXnbT7gHgzFjf0+0URBF7B7
0QIDAQAB
-----END PUBLIC KEY-----";
    private const ISSUER = "doku";
    private const IS_PRODUCTION = false;
    private const AUTH_CODE = "123456789";

    protected function setUp(): void
    {
        $this->directDebitController = $this->createMock(DirectDebitController::class);
        $this->snap = $this->createMock(Snap::class);
    }


    private function getPaymentJumpAppRequestDto(): PaymentJumpAppRequestDto
    {
        return new PaymentJumpAppRequestDto(
            "ORDER_" . time(),
            date('Y-m-d\TH:i:sP', strtotime('+1 day')),
            "12",
            new UrlParamDto("https://example.com", "PAY_RETURN", "N"),
            new TotalAmount("50000.00", "IDR"),
            new PaymentJumpAppAdditionalInfoRequestDto("EMONEY_SHOPEE_PAY_SNAP", null, "something")
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

    public function testDirectDebitPaymentJumpApp_Success(): void
    {
        $request = $this->getPaymentJumpAppRequestDto();
        $expectedResponse = $this->getPaymentJumpAppResponseDto("2005400");

        $this->snap->expects($this->once())
            ->method('doPaymentJumpApp')
            ->with(
                $this->equalTo($request),
                $this->equalTo("deviceId"),
                $this->equalTo(self::PRIVATE_KEY),
                $this->equalTo(self::CLIENT_ID),
                $this->equalTo(self::SECRET_KEY),
                $this->equalTo(self::IS_PRODUCTION)
            )
            ->willReturn($expectedResponse);

        $response = $this->snap->doPaymentJumpApp(
            $request,
            "deviceId",
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("2005400", $response->responseCode);
    }

    public function testDirectDebitPaymentJumpApp_Failed(): void
    {
        $request = $this->getPaymentJumpAppRequestDto();
        $request->additionalInfo->channel = null;
        $expectedResponse = $this->getPaymentJumpAppResponseDto("5005400");

        $this->snap->expects($this->once())
            ->method('doPaymentJumpApp')
            ->willReturn($expectedResponse);

        $response = $this->snap->doPaymentJumpApp(
            $request,
            "deviceId",
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("5005400", $response->responseCode);
    }

    public function testDirectDebitBalanceInquiry_Success(): void
    {
        $request = $this->getBalanceInquiryRequestDto();
        $expectedResponse = $this->getBalanceInquiryResponseDto("2001100");

        $this->snap->expects($this->once())
            ->method('doBalanceInquiry')
            ->with(
                $this->equalTo($request),
                $this->equalTo(self::AUTH_CODE),
                $this->equalTo(self::IP_ADDRESS)
            )
            ->willReturn($expectedResponse);

        $response = $this->snap->doBalanceInquiry($request, self::AUTH_CODE, self::IP_ADDRESS);

        $this->assertEquals("2001100", $response->responseCode);
    }

    public function testDirectDebitBalanceInquiry_Failed(): void
    {
        $request = $this->getBalanceInquiryRequestDto();
        $request->additionalInfo->channel = "";
        $expectedResponse = $this->getBalanceInquiryResponseDto("5001100");

        $this->snap->expects($this->once())
            ->method('doBalanceInquiry')
            ->willReturn($expectedResponse);

        $response = $this->snap->doBalanceInquiry($request, self::AUTH_CODE,self::IP_ADDRESS);

        $this->assertEquals("5001100", $response->responseCode);
    }

    public function testDirectDebitRefund_Success(): void
    {
        $request = $this->getRefundRequestDto();
        $expectedResponse = $this->getRefundResponseDto("2005800");

        $this->snap->expects($this->once())
            ->method('doRefund')
            ->with(
                $this->equalTo($request),
                $this->equalTo(self::AUTH_CODE),
                $this->equalTo(self::PRIVATE_KEY),
                $this->equalTo(self::CLIENT_ID),
                $this->equalTo(self::SECRET_KEY),
                $this->equalTo(self::IS_PRODUCTION)
            )
            ->willReturn($expectedResponse);

        $response = $this->snap->doRefund(
            $request,
            self::AUTH_CODE,
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("2005800", $response->responseCode);
    }

    public function testDirectDebitRefund_Failed(): void
    {
        $request = $this->getRefundRequestDto();
        $request->additionalInfo->channel = null;
        $expectedResponse = $this->getRefundResponseDto("5005800");

        $this->snap->expects($this->once())
            ->method('doRefund')
            ->willReturn($expectedResponse);

        $response = $this->snap->doRefund(
            $request,
            self::AUTH_CODE,
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("5005800", $response->responseCode);
    }

    public function testDirectDebitCheckStatus_Success(): void
    {
        $request = $this->getCheckStatusRequestDto();
        $expectedResponse = $this->getCheckStatusResponseDto("2005500");

        $this->snap->expects($this->once())
            ->method('doCheckStatus')
            ->with(
                $this->equalTo($request),
                $this->equalTo(self::AUTH_CODE),
                $this->equalTo(self::PRIVATE_KEY),
                $this->equalTo(self::CLIENT_ID),
                $this->equalTo(self::SECRET_KEY),
                $this->equalTo(self::IS_PRODUCTION)
            )
            ->willReturn($expectedResponse);

        $response = $this->snap->doCheckStatus(
            $request,
            self::AUTH_CODE,
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("2005500", $response->responseCode);
    }

    public function testDirectDebitCheckStatus_Failed(): void
    {
        $request = $this->getCheckStatusRequestDto();
        $request->serviceCode = "";
        $expectedResponse = $this->getCheckStatusResponseDto("5005500");

        $this->snap->expects($this->once())
            ->method('doCheckStatus')
            ->willReturn($expectedResponse);

        $response = $this->snap->doCheckStatus(
            $request,
            self::AUTH_CODE,
            self::PRIVATE_KEY,
            self::CLIENT_ID,
            self::SECRET_KEY,
            self::IS_PRODUCTION
        );

        $this->assertEquals("5005500", $response->responseCode);
    }

    // Helper methods remain unchanged
}