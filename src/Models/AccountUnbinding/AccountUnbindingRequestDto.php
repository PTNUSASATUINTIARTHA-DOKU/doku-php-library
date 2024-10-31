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

    public function validateAccountUnbindingRequestDto(): void
    {
        if (empty($this->tokenId)) {
            throw new \InvalidArgumentException("Token ID is required");
        }
        if ($this->additionalInfo !== null) {
            $this->additionalInfo->validate();
        } else {
            throw new \InvalidArgumentException("Additional Info is required");
        }
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