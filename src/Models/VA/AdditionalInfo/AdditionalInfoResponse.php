<?php
namespace Doku\Snap\Models\VA\AdditionalInfo;
class AdditionalInfoResponse
{
    public ?string $channel;
    public ?string $howToPayPage;
    public ?string $howToPayIns;
    public function __construct() {
        $this->channel = "";
        $this->howToPayPage = "";
        $this->howToPayIns = "";
    }
}