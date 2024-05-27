<?php

/**
* Class NotificationTokenDto
* This class represents the notification token data transfer object.
*/
class NotificationTokenDto
{
   public NotificationTokenHeaderDto $header;
   public NotificationTokenBodyDto $body;

   /**
    * Constructor for NotificationTokenDto
    *
    * @param NotificationTokenHeaderDto $header
    * @param NotificationTokenBodyDto $body
    */
   public function __construct(NotificationTokenHeaderDto $header, NotificationTokenBodyDto $body)
   {
       $this->header = $header;
       $this->body = $body;
   }
}