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
    * @param PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto
    * @return PaymentNotificationResponseDto
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
   {
       return $this->notificationServices->generateNotificationResponse($paymentNotificationRequestBodyDto);
   }
}