<?php
namespace Doku\Snap\Models\DirectInquiry;
class InquiryReasonDTO
{
    public string $english;
    public string $indonesia;

    /**
     * InquiryReasonDTO constructor.
     *
     * @param string $english
     * @param string $indonesia
     */
    public function __construct(string $english, string $indonesia)
    {
        $this->english = $english;
        $this->indonesia = $indonesia;
    }
}