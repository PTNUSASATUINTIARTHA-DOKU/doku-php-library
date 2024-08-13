<?php
namespace Doku\Snap\Models\AccountUnbinding;
use Doku\Snap\Models\Utilities\AdditionalInfo\AccountUnbindingAdditionalInfoRequestDto;
class AccountUnbindingRequestDto
{
    public ?string $tokenId;
    public ?AccountUnbindingAdditionalInfoRequestDto $additionalInfo;

    public function __construct(?string $tokenId, ?AccountUnbindingAdditionalInfoRequestDto $additionalInfo)
    {
        $this->tokenId = $tokenId;
        $this->additionalInfo = $additionalInfo;
    }

    public function validateAccountUnbindingRequestDto(): void
    {
        if (empty($this->tokenId)) {
            throw new \InvalidArgumentException("Token ID is required");
        }
        $this->additionalInfo->validate();
    }

    public function generateJSONBody(): string
    {
        return json_encode([
            'tokenId' => $this->tokenId,
            'additionalInfo' => array(
                'channel' => $this->additionalInfo->channel,
            )
        ]);
    }
}