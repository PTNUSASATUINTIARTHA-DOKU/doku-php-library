<?php

/**
* Class NotificationTokenDTO
* This class represents the notification token data transfer object.
*/
class NotificationTokenDTO
{
   public NotificationTokenHeaderDTO $header;
   public NotificationTokenBodyDTO $body;

   /**
    * Constructor for NotificationTokenDTO
    *
    * @param NotificationTokenHeaderDTO $header
    * @param NotificationTokenBodyDTO $body
    */
   public function __construct(NotificationTokenHeaderDTO $header, NotificationTokenBodyDTO $body)
   {
       $this->header = $header;
       $this->body = $body;
   }
}