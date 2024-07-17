<?php
namespace Doku\Snap\Commons;

use Exception;
use DateTime;
use DateTimeZone;

use Doku\Snap\Models\RequestHeaderDTO;
class Helper
{
    /**
     * Retrieves the current timestamp in the format 'Y-m-d\TH:i:s+07:00'.
     *
     * @return string The formatted timestamp.
     * @throws Exception If the timestamp generation fails.
     */
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

    public static function prepareHeaders(RequestHeaderDTO $requestHeaderDTO): array
    {
        $result = array(
            "Content-Type: application/json",
            'X-PARTNER-ID: ' . $requestHeaderDTO->xPartnerId,
            'X-EXTERNAL-ID: ' . $requestHeaderDTO->xRequestId,
            'X-TIMESTAMP: ' . $requestHeaderDTO->xTimestamp,
            'X-SIGNATURE: ' . $requestHeaderDTO->xSignature,
            'Authorization: Bearer ' . $requestHeaderDTO->authorization
        );
        if($requestHeaderDTO->channelId != null) {
            array_push($result, 'CHANNEL-ID: ' . $requestHeaderDTO->channelId);
        }
        return $result;
    }

    public static function doHitAPI(string $apiEndpoint, array $headers, string $payload, string $method = "POST"): string
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