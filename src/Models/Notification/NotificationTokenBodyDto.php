<?php

namespace Doku\Snap\Models\Notification;
class NotificationTokenBodyDto
{
   public string $responseCode;
   public string $responseMessage;
   public string $accessToken;
   public string $tokenType;
   public int $expiresIn;
   public string $additionalInfo;

   /**
    * Constructor for NotificationTokenBodyDto
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

    public function generateJSONBody(): string
    {
        $payload = array(
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'accessToken' => $this->accessToken,
            'tokenType' => $this->tokenType,
            'expiresIn' => $this->expiresIn,
            'additionalInfo' => $this->additionalInfo
        );
        return json_encode($payload);
    }
}