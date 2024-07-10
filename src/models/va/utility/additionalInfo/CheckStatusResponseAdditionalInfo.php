<?php

class CheckStatusResponseAdditionalInfo
{
    public $acquirer;

    public function __construct($acquirer = null)
    {
        $this->acquirer = $acquirer;
    }
}