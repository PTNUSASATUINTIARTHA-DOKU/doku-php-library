<?php
namespace Doku\Snap\Models\AccountUnbinding;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;
class AccountUnbindingAdditionalInfoRequestDto
{
    public string $channel;
    public ?Origin $origin;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
        $this->origin = new Origin();
    }

    public function validate()
    {
        // if (!in_array($this->channel, ['DIRECT_DEBIT_MANDIRI_SNAP', 'DIRECT_DEBIT_BRI_SNAP', 'DIRECT_DEBIT_CIMB_SNAP', 'DIRECT_DEBIT_ALLO_SNAP', 'EMONEY_OVO_SNAP'])) {
        //     return [
        //         'responseCode' => '4000701',
        //         'responseMessage' => 'Invalid channel'
        //     ];
        // }
    }
}