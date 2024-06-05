<?php

class Helper
{
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