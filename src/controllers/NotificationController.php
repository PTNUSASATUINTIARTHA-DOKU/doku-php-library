<?php
namespace Doku\Snap\Controllers;

use Doku\Snap\Services\NotificationServices;
use Doku\Snap\Services\TokenServices;
use Doku\Snap\Models\NotificationTokenDto;
use Doku\Snap\Models\PaymentNotificationRequestBodyDTO;
use Doku\Snap\Models\PaymentNotificationResponseDTO;

class NotificationController
{
   private NotificationServices $notificationServices;
   private TokenServices $tokenServices;

   /**
    * NotificationController constructor.
    * @param NotificationServices $notificationService
    */
   public function __construct()
   {
       $this->notificationServices = new NotificationServices();
   }

   /**
    * Generate a notification response based on the provided payment notification request body.
    *
    * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO
    * @return PaymentNotificationResponseDTO
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDTO
   {
       return $this->notificationServices->generateNotificationResponse($paymentNotificationRequestBodyDTO);
   }

    /**
     * Generate an invalid signature response
     *
     * @return NotificationTokenDTO
     */
    public function generateInvalidSignatureResponse(): NotificationTokenDTO
    {
        $timestamp = $this->tokenServices->getTimestamp();
        return $this->tokenServices->generateInvalidSignature($timestamp);
    }

    /**
     * Generate an invalid token response
     * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO
     * @return PaymentNotificationResponseDTO
     */
    public function generateInvalidTokenResponse($paymentNotificationRequestBodyDTO): PaymentNotificationResponseDTO
    {
        return $this->notificationServices->generateInvalidTokenNotificationResponse($paymentNotificationRequestBodyDTO);
    }

}