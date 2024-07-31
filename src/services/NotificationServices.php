<?php
namespace Doku\Snap\Services;

use Doku\Snap\Models\Notification\NotificationTokenHeaderDto;
use Doku\Snap\Models\Notification\NotificationTokenBodyDto;
use Doku\Snap\Models\Notification\NotificationTokenDto;
use Doku\Snap\Models\Notification\NotificationVirtualAccountData;
use Doku\Snap\Models\Notification\PaymentNotificationRequestBodyDto;
use Doku\Snap\Models\Notification\PaymentNotificationResponseDto;
use Doku\Snap\Models\Notification\PaymentNotificationResponseHeaderDto;
use Doku\Snap\Models\Notification\PaymentNotificationResponseBodyDto;
use Doku\Snap\Commons\Helper;

class NotificationServices
{
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
           Helper::getTimestamp()
       );

       return new PaymentNotificationResponseDto(
           $responseHeader,
           $responseBody
       );
   }

    public function generateInvalidSignature(string $timestamp): NotificationTokenDto
    {
        $responseCode = '4017300';
        $responseMessage = 'Unauthorized. Invalid Signature';
        
        $body = new NotificationTokenBodyDto(
            $responseCode,
            $responseMessage,
            null,
            null,
            null, 
            null
        );

        $header = new NotificationTokenHeaderDto(null, $timestamp);

        return new NotificationTokenDto($header, $body);
    }

    public function generateInvalidTokenNotificationResponse(PaymentNotificationRequestBodyDto $paymentNotificationRequestBodyDto): PaymentNotificationResponseDto
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
        
        $body = new PaymentNotificationResponseBodyDto(
            $responseCode,
            $responseMessage,
            $virtualAccountData
        );

        $header = new PaymentNotificationResponseHeaderDto(
            Helper::getTimestamp()
        );

        return new PaymentNotificationResponseDto($header, $body); 
    }
}