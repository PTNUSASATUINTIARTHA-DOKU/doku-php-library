<?php
namespace Doku\Snap\Controllers;

use Doku\Snap\Services\NotificationServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\Notification\NotificationTokenDto;
use Doku\Snap\Models\Notification\PaymentNotificationRequestBodyDto;
use Doku\Snap\Models\Notification\PaymentNotificationResponseDto;

class NotificationController
{
   private NotificationServices $notificationServices;
   private TokenServices $tokenServices;

   public function __construct()
   {
       $this->notificationServices = new NotificationServices();
       $this->tokenServices = new TokenServices();
   }

   public function generateNotificationResponse(PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
   {
       return $this->notificationServices->generateNotificationResponse($paymentNotificationRequestBodyDto);
   }

    public function generateInvalidSignatureResponse(): NotificationTokenDto
    {
        $timestamp = $this->tokenServices->getTimestamp();
        return $this->tokenServices->generateInvalidSignature($timestamp);
    }

    public function generateInvalidTokenResponse(PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
    {
        return $this->notificationServices->generateInvalidTokenNotificationResponse($paymentNotificationRequestBodyDto);
    }

}
