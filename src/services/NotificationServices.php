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
    * @return PaymentNotificationResponseDTO
    */
   public function generateNotificationResponse(PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDTO
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
           Helper::getTimestamp()
       );

       return new PaymentNotificationResponseDTO(
           $responseHeader,
           $responseBody
       );
   }

    /**
     * Generate a NotificationTokenDTO object with invalid signature details
     *
     * @param string $timestamp The timestamp received in the request
     * @return NotificationTokenDTO
     */
    public function generateInvalidSignature(string $timestamp): NotificationTokenDTO
    {
        $responseCode = '4017300';
        $responseMessage = 'Unauthorized. Invalid Signature';
        
        $body = new NotificationTokenBodyDTO(
            $responseCode,
            $responseMessage,
            null,
            null,
            null, 
            null
        );

        $header = new NotificationTokenHeaderDTO(null, $timestamp);

        return new NotificationTokenDTO($header, $body);
    }

    /**
     * Generate a PaymentNotificationResponseDTO object with invalid signature details
     *
     * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO
     * @return PaymentNotificationResponseDTO
     */
    public function generateInvalidTokenNotificationResponse(PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDto): PaymentNotificationResponseDTO
    {
        $responseCode = '4012701';
        $responseMessage = 'invalid Token (B2B)';

        $virtualAccountData = new NotificationVirtualAccountData(
            null,
            null,
            null,
            null,
            null
        );
        
        $body = new PaymentNotificationResponseBodyDTO(
            $responseCode,
            $responseMessage,
            $virtualAccountData
        );

        $header = new PaymentNotificationResponseHeaderDTO(
            Helper::getTimestamp()
        );

        return new PaymentNotificationResponseDTO($header, $body); 
    }
}