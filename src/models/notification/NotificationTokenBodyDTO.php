<?php

/**
* Class NotificationTokenBodyDTO
* This class represents the body portion of the notification token.
*/
class NotificationTokenBodyDTO
{
   public string $responseCode;
   public string $responseMessage;
   public string $accessToken;
   public string $tokenType;
   public int $expiresIn;
   public string $additionalInfo;

   /**
    * Constructor for NotificationTokenBodyDTO
    *
    * @param string $responseCode The response code
    * @param string $responseMessage The response message
    * @param string $accessToken The access token
    * @param string $tokenType The token type
    * @param int $expiresIn The expiration time (in seconds) for the access token
    * @param string $additionalInfo Additional information
    */
   public function __construct(string $responseCode, string $responseMessage, string $accessToken, string $tokenType, ?int $expiresIn, string $additionalInfo)
   {
       $this->responseCode = $responseCode;
       $this->responseMessage = $responseMessage;
       $this->accessToken = $accessToken;
       $this->tokenType = $tokenType;
       $this->expiresIn = $expiresIn;
       $this->additionalInfo = $additionalInfo;
   }
}