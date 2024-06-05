<?php

require_once "src/controllers/TokenController.php";
class DokuSnap
{
    private string $privateKey;
    private string $clientId;
    private bool $isProduction;
    private string $tokenB2B;
    private int $tokenB2BExpiresIn = 900; // 15 minutes (900 seconds)
    private int $tokenB2BGeneratedTimestamp; 
    private string $publicKey;
    private string $issuer;
    private TokenController $tokenB2BController;
    private NotificationController $notificationController;

    /**
     * Constructor
     *
     * @param string $privateKey The private key for authentication
     * @param string $clientId The client ID for authentication
     * @param bool $isProduction Flag indicating whether to use production or sandbox environment
     */
    public function __construct(string $privateKey, string $publicKey, string $clientId, string $issuer, bool $isProduction)
    {
        $this->privateKey = $this->validateString($privateKey);
        $this->publicKey = $this->validateString($publicKey);
        $this->issuer = $this->validateString($issuer);
        $this->clientId = $this->validateString($clientId);
        $this->isProduction = $isProduction;

        $this->tokenB2BController = new TokenController();
        $tokenB2BResponseDTO = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
        $this->setTokenB2B($tokenB2BResponseDTO);
        
        $this->notificationController = new NotificationController();
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
        // Regex (waiting for the sanitation requirements)
        // prefix BRN / RCH (?), length still unknown
        // no empty string, must be char/digit (?)
        $regex = '/[^A-Za-z0-9\-]/';
        return trim(preg_replace($regex, '', $input));
    }

    /**
     * Set the B2B token properties
     *
     * @param TokenB2BResponseDTO $tokenB2BResponseDTO The DTO containing the B2B token response
    */
    public function setTokenB2B(TokenB2BResponseDTO $tokenB2BResponseDTO)
    {
        $this->tokenB2B = $tokenB2BResponseDTO->accessToken;
        $this->tokenExpiresIn = $tokenB2BResponseDTO->expiresIn - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenB2BGeneratedTimestamp = time();

        // TODO
        // The code should be more efficient
        // kalau belum expire jangan lanjutin / tembak lagi 
        // persistent token should be handled
        // redis?
    }

    /**
     * create Virtual Account based on the request
     *
     * @param CreateVaRequestDTO $createVaRequestDTO The DTO containing the create virtual account request
     * @return CreateVaResponseDTO The DTO containing the create virtual account response
    */
    public function createVa($createVaRequestDTO): CreateVaResponseDTO
    {
        // TODO refactor error message
        $status = $createVaRequestDTO->validateVaRequestDTO();
        if(!$status){
            throw new Error();
        }
        // TODO review is it referring to the same token or not
        // what if there are 2 merchant in same time hitting API
        // async or not
        $checkTokenInvalid = $this->tokenB2BController->isTokenInvalid($this->tokenB2B, $this->tokenB2BExpiresIn, $this->tokenB2BGeneratedTimestamp);
        if($checkTokenInvalid){
            $tokenB2BResponseDTO = $this->tokenB2BController->getTokenB2B($this->privateKey, $this->clientId, $this->isProduction);
            $this->setTokenB2B($tokenB2BResponseDTO);
        }	
        $vaController = new VaController();
        $createVAResponseDTO = $vaController->createVa($createVaRequestDTO, $this->privateKey, $this->clientId, $this->tokenB2B, $this->isProduction);
        return $createVAResponseDTO;
    }

    // TODO 2262
    /**
     * Generate a notification response based on token validity and request body
     *
     * @param bool $isTokenValid Whether the token is valid or not
     * @param PaymentNotificationRequestBodyDTO|null $requestBodyDTO The payment notification request body DTO
     * @return PaymentNotificationResponseDTO
     * @throws Exception If the token is valid but the request body DTO is missing
     */
    public function generateNotificationResponse(bool $isTokenValid, ?PaymentNotificationRequestBodyDTO $requestBodyDTO): PaymentNotificationResponseDTO
    {
        if ($isTokenValid) {
            if ($requestBodyDTO !== null) {
                return $this->notificationController->generateNotificationResponse($requestBodyDTO);
            } else {
                throw new Exception('If token is valid, please provide PaymentNotificationRequestBodyDTO');
            }
        } else {
            return $this->notificationController->generateInvalidTokenResponse();
        }
    }

    // TODO 2261
    // public function validateSignature($requestSignature, $requestTimestamp, $privateKey, $clientId): bool
    // {
    //     $tokenB2BController = new TokenController();
    //     $checkTokenValid = $tokenB2BController->validateSignature($requestSignature, $requestTimestamp, $privateKey, $clientId);
    //     return $checkTokenValid;
    // }

     
    // TODO 2258
    // public function validateTokenAndGenerateNotificationResponse($requestHeaderDTO, $paymentNotificationRequestBodyDTO):PaymentNotificationResponseDTO
    // {
    //     return null;
    // }

    // TODO 2257
    // public function validateTokenB2b($requestTokenB2B):boolean{
    //     TokenController.validateTokenB2B(requestTokenB2B, this.publicKey);
    // }

    // TODO 2264
    // public function validateSignatureAndGenerateToken($requestSignature,$requestTimestamp){
    // $isSignatureValid = this.validateSignature(requestSignature, requestTimestamp, this.privateKey, this.clientId);
    // this.generateTokenB2B(isSignatureValid);
    // }

    // TODO 2260
    // public function generateTokenB2B($isSignatureValid:boolean):NotificationTokenDTO{
    //         if(isSignatureValid){
    //                 TokenController.generateTokenB2B(expiredIn, issuer, privateKey, clientId);
    //         }else{
    //                 TokenController.generateInvalidSignatureResponse();
    //         }
    // }


}