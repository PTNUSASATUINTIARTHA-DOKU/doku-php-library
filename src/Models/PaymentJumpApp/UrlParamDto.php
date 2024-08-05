<?php
namespace Doku\Snap\Models\PaymentJumpApp;
class UrlParamDto
{
    public ?string $url;
    public ?string $type;
    public ?string $isDeepLink;

    public function __construct(
        ?string $url,
        ?string $type,
        ?string $isDeepLink
    ) {
        $this->url = $url;
        $this->type = $type ?? 'PAY_RETURN';
        $this->isDeepLink = $isDeepLink ?? 'N';
    }
}