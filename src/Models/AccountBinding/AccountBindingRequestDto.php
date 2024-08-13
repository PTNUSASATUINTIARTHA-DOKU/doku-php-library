<?php
namespace Doku\Snap\Models\AccountBinding;
use Doku\Snap\Models\Utilities\AdditionalInfo\AccountBindingAdditionalInfoRequestDto;
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
            throw new \InvalidArgumentException("Phone number is required");
        }
        $this->additionalInfo->validate();
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