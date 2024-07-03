<?php

class CheckStatusResponseAdditionalInfo extends AdditionalInfo
{
    public string $acquirer;

    public function __construct(string $acquirer)
    {
        $this->acquirer = $acquirer;
    }
}