<?php

class InquiryRequestAdditionalInfoDTO
{
    public string $channel;

    /**
     * InquiryRequestAdditionalInfoDto constructor.
     *
     * @param string $channel The channel information
     */
    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }
}