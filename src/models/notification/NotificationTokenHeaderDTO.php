<?php

namespace Doku\Snap\Models;
class NotificationTokenHeaderDTO
{
   public string $XClientKey;
   public string $XTimeStamp;

   /**
    * Constructor for NotificationTokenHeaderDTO
    *
    * @param string $XClientKey
    * @param string $XTimeStamp
    */
   public function __construct(string $XClientKey, string $XTimeStamp)
   {
       $this->XClientKey = $XClientKey;
       $this->XTimeStamp = $XTimeStamp;
   }
}