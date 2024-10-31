<?php
namespace Doku\Snap\Models\AccountUnbinding;
class AccountUnbindingAdditionalInfoRequestDto
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }

    public function validate(): void {
        if (is_null($this->channel) || trim($this->channel) === '') {
            throw new \InvalidArgumentException("additionalInfo.channel cannot be null or empty. Ensure that additionalInfo.channel is one of the valid channels. Example: 'DIRECT_DEBIT_ALLO_SNAP'.");
        }

        if (!in_array($this->channel, ['DIRECT_DEBIT_MANDIRI_SNAP', 'DIRECT_DEBIT_BRI_SNAP', 'DIRECT_DEBIT_CIMB_SNAP', 'DIRECT_DEBIT_ALLO_SNAP', 'EMONEY_OVO_SNAP'])) {
            throw new \InvalidArgumentException("Invalid channel");
        }
    }
}