<?php
namespace Doku\Snap\Models\Token;
class TokenB2B2CRequestDto
{
    public string $grantType;
    public string $authCode;
    public ?string $refreshToken;
    public ?object $additionalInfo;

    public function __construct(string $grantType, string $authCode, ?string $refreshToken = null, ?object $additionalInfo = null)
    {
        $this->grantType = $grantType;
        $this->authCode = $authCode;
        $this->refreshToken = $refreshToken;
        $this->additionalInfo = $additionalInfo;
    }
}