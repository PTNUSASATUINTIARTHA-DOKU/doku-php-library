<?php
namespace Doku\Snap\Models;
class CheckStatusResponseAdditionalInfo
{
    public $acquirer;

    public function __construct($acquirer = null)
    {
        $this->acquirer = $acquirer;
    }
}