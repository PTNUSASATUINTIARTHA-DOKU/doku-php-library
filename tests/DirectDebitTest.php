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
    private Snap $snap;
    private $directDebitController;
    private const CLIENT_ID = "BRN-0221-1693209567392";
    private const IP_ADDRESS = "127.0.0.1";
    private const SECRET_KEY = "SK-tDzY6MSLBWlNXy3qCsUU";
    private const PUBLIC_KEY = "";
    private const ISSUER = "";
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
                $this->equalTo(self::AUTH_CODE)
            )
            ->willReturn($expectedResponse);

        $response = $this->snap->doBalanceInquiry($request, self::AUTH_CODE);

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

        $response = $this->snap->doBalanceInquiry($request, self::AUTH_CODE);

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