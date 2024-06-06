<?php

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
}