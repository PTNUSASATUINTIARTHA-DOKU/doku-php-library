<?php
namespace Doku\Snap\Models\Token;
class TokenB2B2CResponseDto
{
    public ?string $responseCode;
    public ?string $responseMessage;
    public ?string $accessToken;
    public ?string $tokenType;
    public ?string $accessTokenExpiryTime;
    public ?string $refreshToken;
    public ?string $refreshTokenExpiryTime;
    public ?object $additionalInfo;

    public function __construct(
        ?string $responseCode,
        ?string $responseMessage,
        ?string $accessToken,
        ?string $tokenType,
        ?string $accessTokenExpiryTime,
        ?string $refreshToken,
        ?string $refreshTokenExpiryTime,
        ?object $additionalInfo
    ) {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->accessTokenExpiryTime = $accessTokenExpiryTime;
        $this->refreshToken = $refreshToken;
        $this->refreshTokenExpiryTime = $refreshTokenExpiryTime;
        $this->additionalInfo = $additionalInfo;
    }
}