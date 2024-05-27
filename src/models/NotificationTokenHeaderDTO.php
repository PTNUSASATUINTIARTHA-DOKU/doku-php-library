<?php

/**
* Class NotificationTokenHeaderDto
* This class represents the header portion of the notification token.
*/
class NotificationTokenHeaderDto
{
   public string $XClientKey;
   public string $XTimeStamp;

   /**
    * Constructor for NotificationTokenHeaderDto
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