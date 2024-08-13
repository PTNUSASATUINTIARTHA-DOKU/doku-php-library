<?php
namespace Doku\Snap\Commons;

use Exception;
use DateTime;
use DateTimeZone;

use Doku\Snap\Models\RequestHeader\RequestHeaderDto;
class Helper
{
    public static function getTimestamp($buffer = 0): string {
        try {
            $offset = '+07:00';
            $timestamp = new DateTime('now');
            $timestamp->modify("+$buffer seconds");
            $timestamp->setTimezone(new DateTimeZone($offset));
            return $timestamp->format('c');
        } catch (Exception $e) {
            throw new Exception("Failed to generate timestamp: " . $e->getMessage());
        }
    }

    public static function prepareHeaders(RequestHeaderDto $requestHeaderDto): array
    {
        $result = array(
            "Content-Type: application/json",
            'X-PARTNER-ID: ' . $requestHeaderDto->xPartnerId,
            'X-EXTERNAL-ID: ' . $requestHeaderDto->xRequestId,
            'X-TIMESTAMP: ' . $requestHeaderDto->xTimestamp,
            'X-SIGNATURE: ' . $requestHeaderDto->xSignature,
            'Authorization: Bearer ' . $requestHeaderDto->authorization
        );
        if($requestHeaderDto->channelId != null) {
            array_push($result, 'CHANNEL-ID: ' . $requestHeaderDto->channelId);
        }
        if($requestHeaderDto->ipAddress != null) {
            array_push($result, 'X-IP-ADDRESS: ' . $requestHeaderDto->ipAddress);
        }
        if($requestHeaderDto->deviceId != null) {
            array_push($result, 'X-DEVICE-ID: ' . $requestHeaderDto->deviceId);
        }
        if($requestHeaderDto->xChannelId != null) {
            array_push($result, 'X-CHANNEL-ID: ' . $requestHeaderDto->xChannelId);
        }
        if($requestHeaderDto->tokenB2B2C != null) {
            array_push($result, 'X-TOKEN-B2B-2C: ' . $requestHeaderDto->tokenB2B2C);
        }
        return $result;
    }

    public static function doHitAPI(string $apiEndpoint, array $headers, string $payload, string $method): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        switch ($method) {
            case "GET":
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                break;
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch); 
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        curl_close($ch);
        return $response;
    }

    public static function generateExternalId(): string
    {
        // Generate a UUID and combine the UUID and timestamp
        $uuid = bin2hex(random_bytes(16));
        $externalId = $uuid  . Helper::getTimestamp();

        return $externalId;
    }

    public static function generateRequestHeaderDto(
        string $timestamp,
        string $signature,
        string $clientId,
        string $externalId,
        ?string $channelId = null,
        string $tokenB2B,
        ?string $ipAddress = null,
        ?string $deviceId = null,
        ?string $tokenB2B2C = null
    ): RequestHeaderDto {
        $requestHeaderDto = new RequestHeaderDto(
            $timestamp,
            $signature,
            $clientId,
            $externalId,
            $channelId,
            $tokenB2B,
            $ipAddress,
            $deviceId,
            $tokenB2B2C
        );
        return $requestHeaderDto;
    }
}