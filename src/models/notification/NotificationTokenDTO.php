<?php

namespace Doku\Snap\Models\Notification;
class NotificationTokenDTO
{
    public NotificationTokenHeaderDTO $header;
    public NotificationTokenBodyDTO $body;

    /**
        * Constructor for NotificationTokenDTO
        *
        * @param NotificationTokenHeaderDTO $header
        * @param NotificationTokenBodyDTO $body
        */
    public function __construct(NotificationTokenHeaderDTO $header, NotificationTokenBodyDTO $body)
    {
        $this->header = $header;
        $this->body = $body;
    }

    public function generateJSONHeader(): string
    {
        return $this->header->generateJSONHeader();
    }

    public function generateJSONBody(): string
    {
        return $this->body->generateJSONBody();
    }
}