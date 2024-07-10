<?php
class CreateVaResponseAdditionalInfo
{
    public ?string $channel;
    public ?string $howToPayPage;
    public ?string $howToPayApi;

    public function __construct(?string $channel, ?string $howToPayPage, ?string $howToPayApi)
    {
        $this->channel = $channel;
        $this->howToPayPage = $howToPayPage;
        $this->howToPayApi = $howToPayApi;
    }
}