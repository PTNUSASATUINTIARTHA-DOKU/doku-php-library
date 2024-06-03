<?php

/**
* Class NotificationController
*/
class NotificationServices
{
   /**
    * Generate a notification response based on the provided payment notification request body.
    *
    * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO
    * @return PaymentNotificationResponseDto
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDto
   {
       $responseCode = '2002700';
       $responseMessage = 'success';

       $virtualAccountData = new NotificationVirtualAccountData(
           $paymentNotificationRequestBodyDTO->partnerServiceId,
           $paymentNotificationRequestBodyDTO->customerNo,
           $paymentNotificationRequestBodyDTO->virtualAccountNo,
           $paymentNotificationRequestBodyDTO->virtualAccountName,
           $paymentNotificationRequestBodyDTO->paymentRequestId
       );

       $responseBody = new PaymentNotificationResponseBodyDTO(
           $responseCode,
           $responseMessage,
           $virtualAccountData
       );

       $responseHeader = new PaymentNotificationResponseHeaderDTO(
           date('Y-m-d H:i:s') // TODO fix this
       );

       return new PaymentNotificationResponseDto(
           $responseHeader,
           $responseBody
       );
   }
}