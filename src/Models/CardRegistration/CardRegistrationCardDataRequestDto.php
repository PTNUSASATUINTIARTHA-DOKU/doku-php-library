<?php
namespace Doku\Snap\Models\CardRegistration;
class CardRegistrationCardDataRequestDto
{
    public ?string $bankCardNo;
    public ?string $bankCardType;
    public ?string $expiryDate;
    public ?string $identificationNo;
    public ?string $identificationType;
    public ?string $email;

    public function __construct(
        ?string $bankCardNo,
        ?string $bankCardType,
        ?string $expiryDate,
        ?string $identificationNo,
        ?string $identificationType,
        ?string $email
    ) {
        $this->bankCardNo = $bankCardNo;
        $this->bankCardType = $bankCardType;
        $this->expiryDate = $expiryDate;
        $this->identificationNo = $identificationNo;
        $this->identificationType = $identificationType;
        $this->email = $email;
    }

    public function validate(): void
    {
        if (empty($this->bankCardNo) || strlen($this->bankCardNo) > 20) {
            throw new \InvalidArgumentException("bankCardNo is required and must not exceed 20 characters");
        }
    
        if (empty($this->bankCardType)) {
            throw new \InvalidArgumentException("bankCardType is required");
        }
    
        if (empty($this->expiryDate) || strlen($this->expiryDate) !== 4 || !preg_match('/^(0[1-9]|1[0-2])[0-9]{2}$/', $this->expiryDate)) {
            throw new \InvalidArgumentException("expiryDate is required in format MMYY");
        }
    
            // Validasi optional fields
        if ($this->identificationNo !== null) {
            if (!is_string($this->identificationNo)) {
                throw new \InvalidArgumentException("identificationNo must be a string");
            }
        }

        if ($this->identificationType !== null) {
            if (!is_string($this->identificationType)) {
                throw new \InvalidArgumentException("identificationType must be a string");
            }
        }

        if ($this->email !== null) {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("email must be a valid email address");
            }
        }
    }
    

    public function generateJSONBody(): array
    {
        return [
            'bankCardNo' => $this->bankCardNo,
            'bankCardType' => $this->bankCardType,
            'expiryDate' => $this->expiryDate,
            'identificationNo' => $this->identificationNo,
            'identificationType' => $this->identificationType,
            'email' => $this->email,
        ];
    }
}