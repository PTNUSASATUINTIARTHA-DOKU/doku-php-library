<?php
namespace Doku\Snap\Models\Token;
class TokenB2BResponseDto
{
    public string $responseCode;
    public string $responseMessage;
    public string $accessToken;
    public string $tokenType;
    public int $expiresIn;
    public string $additionalInfo;

    public function __construct(string $responseCode, string $responseMessage, string $accessToken, string $tokenType, int $expiresIn, string $additionalInfo)
    {
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->additionalInfo = $additionalInfo;
    }
}