<?php

namespace Doku\Snap;
use PHPUnit\Framework\TestCase;

class SnapTest extends TestCase
{
    private Snap $snap;
    private $tokenB2BController;

    protected function setUp(): void
    {
        $this->tokenB2BController = $this->createMock(Controllers\TokenController::class);
        $this->snap = new Snap($this->tokenB2BController);
    }

    public function testGetB2BToken_SuccessProduction()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("2007300", "production_token"));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, self::CLIENT_ID, true);

        $this->assertEquals("production_token", $response);
    }

    public function testGetB2BToken_SuccessUAT()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("2007300", "uat_token"));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, self::CLIENT_ID, false);

        $this->assertEquals("uat_token", $response);
    }

    public function testGetB2BToken_FailedInvalidPrivateKey()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("5007300", null));

        $response = $this->snap->getB2BToken("INVALID_PRIVATE_KEY", self::CLIENT_ID, false);

        $this->assertNull($response);
    }

    public function testGetB2BToken_FailedInvalidPublicKey()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("5007300", null));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, "INVALID_PUBLIC_KEY", false);

        $this->assertNull($response);
    }

    public function testGetB2BToken_FailedInvalidClientID()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("5007300", null));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, "INVALID_CLIENT_ID", false);

        $this->assertNull($response);
    }

    public function testGetB2BToken_FailedInvalidIssuer()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("5007300", null));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, self::CLIENT_ID, false);

        $this->assertNull($response);
    }

    public function testGetB2BToken_FailedInvalidSecretKey()
    {
        $this->tokenB2BController->method('getTokenB2B')
            ->willReturn($this->getTokenB2BResponseDto("5007300", null));

        $response = $this->snap->getB2BToken(self::PRIVATE_KEY, self::CLIENT_ID, false);

        $this->assertNull($response);
    }

    private function getTokenB2BResponseDto(string $responseCode, ?string $token): TokenB2BResponseDto
    {
        $dto = new TokenB2BResponseDto();
        $dto->setResponseCode($responseCode);
        $dto->setToken($token);
        return $dto;
    }
}