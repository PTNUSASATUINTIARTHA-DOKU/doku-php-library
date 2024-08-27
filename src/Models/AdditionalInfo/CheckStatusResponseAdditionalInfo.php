<?php
namespace Doku\Snap\Models\AdditionalInfo;
class CheckStatusResponseAdditionalInfo
{
    public $acquirer;

    public function __construct($acquirer = null)
    {
        $this->acquirer = $acquirer;
    }
}