<?php

/**
* Class NotificationController
*/
class NotificationServices
{
   /**
    * Generate a notification response based on the provided payment notification request body.
    *
    * @param PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto
    * @return PaymentNotificationResponseDto
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
   {
       $responseCode = '2002700';
       $responseMessage = 'success';

       $virtualAccountData = new NotificationVirtualAccountData(
           $paymentNotificationRequestBodyDto->partnerServiceId,
           $paymentNotificationRequestBodyDto->customerNo,
           $paymentNotificationRequestBodyDto->virtualAccountNo,
           $paymentNotificationRequestBodyDto->virtualAccountName,
           $paymentNotificationRequestBodyDto->paymentRequestId
       );

       $responseBody = new PaymentNotificationResponseBodyDto(
           $responseCode,
           $responseMessage,
           $virtualAccountData
       );

       $responseHeader = new PaymentNotificationResponseHeaderDto(
           date('Y-m-d H:i:s') // TODO fix this
       );

       return new PaymentNotificationResponseDto(
           $responseHeader,
           $responseBody
       );
   }
}