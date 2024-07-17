<?php
namespace Doku\Snap\Models;
class TokenB2BRequestDTO
{
    public string $signature;
    public string $timestamp;
    public string $clientId;
    public string $grantType = 'client_credentials';

    /**
     * Constructor for TokenB2BRequestDTO
     *
     * @param string $signature The signature for authentication
     * @param string $timestamp The timestamp for the request
     * @param string $clientId The client ID for authentication
     */
    public function __construct(string $signature, string $timestamp, string $clientId)
    {
        $this->signature = $signature;
        $this->timestamp = $timestamp;
        $this->clientId = $clientId;
    }
}