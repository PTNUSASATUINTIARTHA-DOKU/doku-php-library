<?php

class CheckStatusResponsePaymentFlagReason
{
    public ?string $english;
    public ?string $indonesia;

    public function __construct(?string $english, ?string $indonesia)
    {
        $this->english = $english;
        $this->indonesia = $indonesia;
    }
}