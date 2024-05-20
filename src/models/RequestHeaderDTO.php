<?php
/**
 * Class RequestHeaderDTO
 * Represents the request header data transfer object
 */
class RequestHeaderDTO
{
    public string $xTimestamp;
    public string $xSignature;
    public string $xPartnerId;
    public string $xRequestId;
    public string $channelId;
    public string $authorization;
}