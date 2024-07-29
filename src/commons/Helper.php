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
}