<?php
namespace Doku\Snap\Models\AccountUnbinding;
class AccountUnbindingRequestDto
{
    public ?string $tokenId;
    public ?AccountUnbindingAdditionalInfoRequestDto $additionalInfo;

    public function __construct(?string $tokenId, ?AccountUnbindingAdditionalInfoRequestDto $additionalInfo)
    {
        $this->tokenId = $tokenId;
        $this->additionalInfo = $additionalInfo;
    }

    public function validateAccountUnbindingRequestDto()
    {
        if (empty($this->tokenId)) {
            return [
                'responseCode' => '4000701',
                'responseMessage' => 'tokenId is required'
            ];
        }
        $this->additionalInfo->validate();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'tokenId' => $this->tokenId,
            'additionalInfo' => array(
                'channel' => $this->additionalInfo->channel,
                'origin' => $this->additionalInfo->origin->toArray()
            )
        ]);
    }
}