<?php
namespace Doku\Snap\Models\Utilities\AdditionalInfo;
class CheckStatusResponseAdditionalInfo
{
    public $acquirer;

    public function __construct($acquirer = null)
    {
        $this->acquirer = $acquirer;
    }
}