<?php
namespace Doku\Snap\Models;
class CreateVaRequestDtoV1
{
    public ?string $mallId;
    public ?string $chainMerchant;
    public ?string $amount;
    public ?string $purchaseAmount;
    public ?string $transIdMerchant;
    public ?string $PaymentType;
    public ?string $words;
    public ?string $requestDateTime;
    public ?string $currency;
    public ?string $purchaseCurrency;
    public ?string $sessionId;
    public ?string $name;
    public ?string $email;
    public ?string $additionalData;
    public ?string $basket;
    public ?string $shippingAddress;
    public ?string $shippingCity;
    public ?string $shippingState;
    public ?string $shippingCountry;
    public ?string $shippingZipcode;
    public ?string $paymentChannel;
    public ?string $address;
    public ?string $city;
    public ?string $state;
    public ?string $country;
    public ?string $zipcode;
    public ?string $homephone;
    public ?string $mobilephone;
    public ?string $workphone;
    public ?string $birthday;
    public ?string $partnerServiceId;
    public ?string $expiredDate;

    public function __construct(
        ?string $mallId = null,
        ?string $chainMerchant = null,
        ?string $amount = null,
        ?string $purchaseAmount = null,
        ?string $transIdMerchant = null,
        ?string $PaymentType = null,
        ?string $words = null,
        ?string $requestDateTime = null,
        ?string $currency = null,
        ?string $purchaseCurrency = null,
        ?string $sessionId = null,
        ?string $name = null,
        ?string $email = null,
        ?string $additionalData = null,
        ?string $basket = null,
        ?string $shippingAddress = null,
        ?string $shippingCity = null,
        ?string $shippingState = null,
        ?string $shippingCountry = null,
        ?string $shippingZipcode = null,
        ?string $paymentChannel = null,
        ?string $address = null,
        ?string $city = null,
        ?string $state = null,
        ?string $country = null,
        ?string $zipcode = null,
        ?string $homephone = null,
        ?string $mobilephone = null,
        ?string $workphone = null,
        ?string $birthday = null,
        ?string $partnerServiceId = null,
        ?string $expiredDate = null
    ) {
        $this->mallId = $mallId;
        $this->chainMerchant = $chainMerchant;
        $this->amount = $amount;
        $this->purchaseAmount = $purchaseAmount;
        $this->transIdMerchant = $transIdMerchant;
        $this->PaymentType = $PaymentType;
        $this->words = $words;
        $this->requestDateTime = $requestDateTime;
        $this->currency = $currency;
        $this->purchaseCurrency = $purchaseCurrency;
        $this->sessionId = $sessionId;
        $this->name = $name;
        $this->email = $email;
        $this->additionalData = $additionalData;
        $this->basket = $basket;
        $this->shippingAddress = $shippingAddress;
        $this->shippingCity = $shippingCity;
        $this->shippingState = $shippingState;
        $this->shippingCountry = $shippingCountry;
        $this->shippingZipcode = $shippingZipcode;
        $this->paymentChannel = $paymentChannel;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->zipcode = $zipcode;
        $this->homephone = $homephone;
        $this->mobilephone = $mobilephone;
        $this->workphone = $workphone;
        $this->birthday = $birthday;
        $this->partnerServiceId = $partnerServiceId;
        $this->expiredDate = $expiredDate;
    }

 
     /**
      * Converts the current object to a CreateVaRequestDto object.
      *
      * @return CreateVaRequestDto The converted CreateVaRequestDto object.
      */
     public function convertToCreateVaRequestDTO(): CreateVaRequestDTO
     {
         $totalAmount = new TotalAmount($this->amount, $this->currency);
         $additionalInfo = new CreateVaRequestAdditionalInfo($this->paymentChannel, new CreateVaVirtualAccountConfig(false));
 
         return new CreateVaRequestDTO(
             $this->partnerServiceId,
             null,
             null,
             $this->name,
             $this->email,
             $this->mobilephone,
             $this->transIdMerchant,
             $totalAmount,
             $additionalInfo,
             "1",
             $this->expiredDate
         );
     }
 }
