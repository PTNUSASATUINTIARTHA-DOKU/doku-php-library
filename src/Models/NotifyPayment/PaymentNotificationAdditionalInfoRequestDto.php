<?php
namespace Doku\Snap\Models\NotifyPayment;
use Doku\Snap\Models\VA\AdditionalInfo\Origin;
use Doku\Snap\Models\Payment\LineItemsDto;

class PaymentNotificationAdditionalInfoRequestDto
{
    public string $channelId;
    public string $acquirerId;
    public string $custIdMerchant;
    public string $accountType;
    /** @var LineItemsDto[] */
    public array $lineItems;
    public Origin $origin;

    /**
     * @param LineItemsDto[] $lineItems
     */
    public function __construct(
        string $channelId,
        string $acquirerId,
        string $custIdMerchant,
        string $accountType,
        array $lineItems,
        Origin $origin
    ) {
        $this->channelId = $channelId;
        $this->acquirerId = $acquirerId;
        $this->custIdMerchant = $custIdMerchant;
        $this->accountType = $accountType;
        $this->lineItems = $lineItems;
        $this->origin = $origin;
    }

    public function generateJSONBody(): array
    {
        return [
            'channelId' => $this->channelId,
            'acquirerId' => $this->acquirerId,
            'custIdMerchant' => $this->custIdMerchant,
            'accountType' => $this->accountType,
            'lineItems' => array_map(function ($item) {
                return $item->generateJSONBody();
            }, $this->lineItems),
            'origin' => $this->origin->toArray(),
        ];
    }
}