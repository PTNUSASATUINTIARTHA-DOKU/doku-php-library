<?php
namespace Doku\Snap\Models\AccountBinding;
class AccountBindingRequestDto
{
    public ?string $phoneNo;
    public ?AccountBindingAdditionalInfoRequestDto $additionalInfo;

    public function __construct(?string $phoneNo, ?AccountBindingAdditionalInfoRequestDto $additionalInfo)
    {
        $this->phoneNo = $phoneNo;
        $this->additionalInfo = $additionalInfo;
    }

    public function validateAccountBindingRequestDto(): void
    {
        if (empty($this->phoneNo)) {
            throw new \InvalidArgumentException("Phone number is required. Example: '62813941306101'.");
        }
        
        if (strlen($this->phoneNo) < 9) {
            throw new \InvalidArgumentException("phoneNo must be at least 9 digits. Ensure that phoneNo is not empty. Example: '62813941306101'.");
        }
    
        if (strlen($this->phoneNo) > 16) {
            throw new \InvalidArgumentException("phoneNo must be 16 characters or fewer. Ensure that phoneNo is no longer than 16 characters. Example: '62813941306101'.");
        }
    
        if ($this->additionalInfo !== null) {
            $this->additionalInfo->validate();
        } else {
            throw new \InvalidArgumentException("Additional Info is required");
        }
    }

    public function generateJSONBody(): string
    {
        $additionalInfoArr = array(
            'channel' => $this->additionalInfo->channel,
            'custIdMerchant' => $this->additionalInfo->custIdMerchant,
            'customerName' => $this->additionalInfo->customerName,
            'email' => $this->additionalInfo->email,
            'idCard' => $this->additionalInfo->idCard,
            'country' => $this->additionalInfo->country,
            'address' => $this->additionalInfo->address,
            'dateOfBirth' => $this->additionalInfo->dateOfBirth,
            'successRegistrationUrl' => $this->additionalInfo->successRegistrationUrl,
            'failedRegistrationUrl' => $this->additionalInfo->failedRegistrationUrl,
            'deviceModel' => $this->additionalInfo->deviceModel,
            'osType' => $this->additionalInfo->osType,
            'channelId' => $this->additionalInfo->channelId
        );
        return json_encode([
            'phoneNo' => $this->phoneNo,
            'additionalInfo' => $additionalInfoArr
        ]);
    }
}