<?php
namespace Doku\Snap\Models\PaymentJumpApp;

use Doku\Snap\Models\VA\AdditionalInfo\Origin;
class PaymentJumpAppAdditionalInfoResponseDto
{
    public ?string $webRedirectUrl;

    public function __construct(
        ?string $webRedirectUrl,
    ) {
        $this->webRedirectUrl = $webRedirectUrl;
    }
}