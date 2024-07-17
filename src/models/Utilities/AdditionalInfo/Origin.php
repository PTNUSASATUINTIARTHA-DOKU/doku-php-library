<?php
namespace Doku\Snap\Models\Utilities\AdditionalInfo;
class Origin
{
    public string $product = "SDK";
    public string $source;
    public string $sourceVersion;
    public string $system;
    public string $apiFormat = "SNAP";

    public function __construct(string $source="PHP", string $sourceVersion="1.0.0", string $system="doku-php-library")
    {
        $this->source = $source;
        $this->sourceVersion = $sourceVersion;
        $this->system = $system;
    }

    public function toArray(): array
    {
        return [
            'product' => $this->product,
            'source' => $this->source,
            'sourceVersion' => $this->sourceVersion,
            'system' => $this->system,
            'apiFormat' => $this->apiFormat
        ];
    }
}