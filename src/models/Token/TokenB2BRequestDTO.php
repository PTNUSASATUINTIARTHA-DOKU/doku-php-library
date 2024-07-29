<?php
namespace Doku\Snap\Models\Token;
class TokenB2BRequestDto
{
    public string $signature;
    public string $timestamp;
    public string $clientId;
    public string $grantType = 'client_credentials';

    public function __construct(string $signature, string $timestamp, string $clientId)
    {
        $this->signature = $signature;
        $this->timestamp = $timestamp;
        $this->clientId = $clientId;
    }
}