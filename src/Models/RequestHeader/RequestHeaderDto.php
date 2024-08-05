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
    public ?string $ipAddress;
    public ?string $deviceId;
    public ?string $xChannelId;
    public function __construct(
        string $xTimestamp,
        string $xSignature,
        string $xPartnerId,
        string $xRequestId,
        ?string $channelId = "SDK",
        string $authorization,
        ?string $ipAddress,
        ?string $deviceId,
        ?string $xChannelId = "H2H"
    ) {
        $this->xTimestamp = $xTimestamp;
        $this->xSignature = $xSignature;
        $this->xPartnerId = $xPartnerId;
        $this->xRequestId = $xRequestId;
        $this->channelId = $channelId;
        $this->authorization = $authorization;
        $this->ipAddress = $ipAddress;
        $this->deviceId = $deviceId;
        $this->xChannelId = $xChannelId;
    }
}