<?php
namespace Doku\Snap\Models\AdditionalInfo;
class AccountUnbindingAdditionalInfoRequestDto
{
    public string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }

    public function validate(): void
    {
        if (!in_array($this->channel, ['Mandiri', 'BRI', 'CIMB', 'Allo', 'OVO'])) {
            throw new \InvalidArgumentException("Invalid channel");
        }
    }
}