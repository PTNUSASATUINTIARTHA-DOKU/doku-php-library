<?php

namespace Doku\Snap\Models\Notification;
class NotificationTokenDto
{
    public NotificationTokenHeaderDto $header;
    public NotificationTokenBodyDto $body;

    public function __construct(NotificationTokenHeaderDto $header, NotificationTokenBodyDto $body)
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