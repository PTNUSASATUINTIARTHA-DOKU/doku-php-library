<?php

namespace Doku\Snap\Models\Notification;
class NotificationTokenHeaderDTO
{
    public string $XClientKey;
    public string $XTimeStamp;

    /**
    * Constructor for NotificationTokenHeaderDTO
    *
    * @param string $XClientKey
    * @param string $XTimeStamp
    */
    public function __construct(string $XClientKey, string $XTimeStamp)
    {
        $this->XClientKey = $XClientKey;
        $this->XTimeStamp = $XTimeStamp;
    }
    public function generateJSONHeader(): string
    {
        $payload = array(
            'XClientKey' => $this->XClientKey,
            'XTimeStamp' => $this->XTimeStamp
        );
        return json_encode($payload);
    }
}