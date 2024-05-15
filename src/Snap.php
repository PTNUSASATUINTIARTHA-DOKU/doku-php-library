<?php

require_once 'src/controllers/TokenControllers.php';
class DokuSnap
{
    private $privateKey;
    private $clientId;
    private $isProduction;
    private $tokenB2B;
    private $tokenB2BExpiresIn = 900; // 15 minutes (900 seconds)
    private $tokenB2BGeneratedTimestamp;

    /**
     * Constructor
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param bool $isProduction Flag indicating whether to use production or sandbox environment
     */
    public function __construct(string $privateKey, string $clientId, bool $isProduction)
    {
        $this->privateKey = $this->validateString($privateKey);
        $this->clientId = $this->validateString($clientId);
        $this->isProduction = $isProduction;

        $tokenB2BController = new TokenController();
        $tokenB2BResponseDTO = $tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
        $this->setTokenB2B($tokenB2BResponseDTO);
    }

    /**
     * Validates and sanitizes the input string
     *
     * @param string $input The input string to validate
     * @return string The sanitized string
     */
    private function validateString(string $input): string
    {
        // Perform string validation and sanitization here
        // TODO
        //Regex (waiting for the sanitation requirements)
        // prefix BRN / RCH (?), length still unknown
        // no empty string, must be char/digit (?)
        $regex = '/[^A-Za-z0-9\-]/';
        return trim(preg_replace($regex, '', $input));
    }

    /**
     * Set the B2B token properties
     *
     * @param TokenB2BResponseDTO $tokenB2BResponseDto The DTO containing the B2B token response
    */
    public function setTokenB2B(TokenB2BResponseDTO $tokenB2BResponseDto)
    {
        $this->tokenB2B = $tokenB2BResponseDto->accessToken;
        $this->tokenExpiresIn = $tokenB2BResponseDto->expiresIn - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenTimestamp = time(); // Get the current Unix timestamp

        // kalau belum expire jangan lanjutin / tembak lagi 
        // redis?
        // 
    }
}