<?php
namespace Doku\Snap\Models\VA\AdditionalInfo;
class CheckStatusVaResponseAdditionalInfo
{
    public $acquirer;

    public function __construct($acquirer = null)
    {
        $this->acquirer = $acquirer;
    }
}