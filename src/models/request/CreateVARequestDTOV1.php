<?php

class CreateVaRequestDtoV1
{
    public string $mallId;
    public string $chainMerchant;
    public string $amount;
    public string $purchaseAmount;
    public string $transIdMerchant;
    public string $PaymentType;
    public string $words;
    public string $requestDateTime;
    public string $currency;
    public string $purchaseCurrency;
    public string $sessionId;
    public string $name;
    public string $email;
    public string $additionalData;
    public string $basket;
    public string $shippingAddress;
    public string $shippingCity;
    public string $shippingState;
    public string $shippingCountry;
    public string $shippingZipcode;
    public string $paymentChannel;
    public string $address;
    public string $city;
    public string $state;
    public string $country;
    public string $zipcode;
    public string $homephone;
    public string $mobilephone;
    public string $workphone;
    public string $birthday;
    public string $partnerServiceId;
    public string $expiredDate;

    /**
     * Converts the current object to a CreateVaRequestDto object.
     *
     * @return CreateVaRequestDto The converted CreateVaRequestDto object.
     */
    public function convertToCreateVaRequestDTO(): CreateVaRequestDTO
    {
        $totalAmount = new TotalAmount($this->amount, $this->currency);
        $additionalInfo = new AdditionalInfo($this->paymentChannel, new VirtualAccountConfig(false));

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