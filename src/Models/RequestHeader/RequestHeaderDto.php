<?php
namespace Doku\Snap\Models\RequestHeader;
class RequestHeaderDto
{
    public string $xTimestamp;
    public string $xSignature;
    public string $xPartnerId;
    public string $xRequestId;
    public ?string $channelId;
    public string $authorization;
    public function __construct(
        string $xTimestamp,
        string $xSignature,
        string $xPartnerId,
        string $xRequestId,
        ?string $channelId,
        string $authorization
    ) {
        $this->xTimestamp = $xTimestamp;
        $this->xSignature = $xSignature;
        $this->xPartnerId = $xPartnerId;
        $this->xRequestId = $xRequestId;
        $this->channelId = "SDK";
        $this->authorization = $authorization;
    }
}