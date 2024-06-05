<?php

class Helper
{
    /**
     * Retrieves the current timestamp in the format 'Y-m-d\TH:i:s+07:00'.
     *
     * @return string The formatted timestamp.
     * @throws Exception If the timestamp generation fails.
     */
    public static function getTimestamp(): string {
        try {
            $currentTimestamp = time();
            $formattedTimestamp = gmdate('Y-m-d\TH:i:s+07:00', $currentTimestamp);
            return $formattedTimestamp;
        } catch (Exception $e) {
            throw new Exception("Failed to generate timestamp: " . $e->getMessage());
        }
    }
}