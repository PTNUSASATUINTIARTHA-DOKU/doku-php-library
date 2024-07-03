<?php

class DeleteVaRequestAdditionalInfo extends AdditionalInfo
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }
}