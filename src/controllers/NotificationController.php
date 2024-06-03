<?php

class NotificationController
{
   private NotificationServices $notificationServices;

   /**
    * NotificationController constructor.
    * @param NotificationServices $notificationService
    */
   public function __construct(NotificationServices $notificationServices)
   {
       $this->notificationServices = $notificationServices;
   }

   /**
    * Generate a notification response based on the provided payment notification request body.
    *
    * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO
    * @return PaymentNotificationResponseDto
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDto
   {
       return $this->notificationServices->generateNotificationResponse($paymentNotificationRequestBodyDTO);
   }
}