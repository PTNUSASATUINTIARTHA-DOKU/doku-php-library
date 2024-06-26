<?php
require_once "src/controllers/TokenController.php";
require_once "src/controllers/NotificationController.php";
require_once "src/controllers/VaController.php";
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
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->issuer = $issuer;
        $this->clientId =$clientId;
        $this->isProduction = $isProduction;

        $this->tokenB2BController = new TokenController();
        $this->notificationController = new NotificationController();

        $tokenB2BResponseDTO = $this->tokenB2BController->getTokenB2B($privateKey, $clientId, $isProduction);
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
        $this->tokenB2BExpiresIn = $tokenB2BResponseDTO->expiresIn - 10; // Subtract 10 seconds as in diagram requirements
        $this->tokenB2BGeneratedTimestamp = time();

        // TODO
        // The code should be more efficient
        // kalau belum expire jangan lanjutin / tembak lagi 
        // persistent token should be handled
        // redis?
    }


    /**
     * ONLY FOR TESTING
     * Get the token, timestamp, and expiration time of the B2B token
     *
     * @return string The token, timestamp, and expiration time of the B2B token
     */
    public function getTokenAndTime(): string
    {
        $string = $this->tokenB2B . PHP_EOL;
        $string = $string . "Generated timestamp: " . $this->tokenB2BGeneratedTimestamp . PHP_EOL;
        return $string  . "Expired In: " . $this->tokenB2BExpiresIn . PHP_EOL;
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

    /**
     * Generate a notification response based on token validity and request body
     *
     * @param bool $isTokenValid Whether the token is valid or not
     * @param PaymentNotificationRequestBodyDTO|null $requestBodyDTO The payment notification request body DTO
     * @return PaymentNotificationResponseDTO
     * @throws Exception If the token is valid but the request body DTO is missing
     */
    public function generateNotificationResponse(bool $isTokenValid, ?PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDTO
    {
        if ($isTokenValid) {
            if ($paymentNotificationRequestBodyDTO !== null) {
                return $this->notificationController->generateNotificationResponse($paymentNotificationRequestBodyDTO);
            } else {
                throw new Exception('If token is valid, please provide PaymentNotificationRequestBodyDTO');
            }
        } else {
            return $this->notificationController->generateInvalidTokenResponse($paymentNotificationRequestBodyDTO);
        }
    }

    /**
     * Validates the signature received in the request.
     *
     * @param string $requestSignature The signature received in the request.
     * @param string $requestTimestamp The timestamp received in the request.
     * @param string $privateKey The private key for authentication.
     * @param string $clientId The client ID for authentication.
     *
     * @return bool True if the signature is valid, false otherwise.
     */
    public function validateSignature(string $requestSignature, string $requestTimestamp, string $privateKey, string $clientId): bool
    {
        // Call the validateSignature method of the tokenB2BController to validate the signature
        return $this->tokenB2BController->validateSignature($requestSignature, $requestTimestamp, $privateKey, $clientId);
    }

     
    /**
     * Validates the TokenB2B received in the request and generates a notification response.
     *
     * This method is responsible for validating the TokenB2B received in the request and generating a notification response.
     * If the TokenB2B is valid, it calls the `generateNotificationResponse` method of the `NotificationController` to generate the notification response.
     *
     * @param RequestHeaderDTO $requestHeaderDTO The header DTO containing the TokenB2B for validation.
     * @param PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO The payment notification request body DTO.
     * @return PaymentNotificationResponseDTO The generated notification response.
     */
    public function validateTokenAndGenerateNotificationResponse(RequestHeaderDTO $requestHeaderDTO, PaymentNotificationRequestBodyDTO $paymentNotificationRequestBodyDTO): PaymentNotificationResponseDTO
    {
        $isTokenValid = $this->validateTokenB2B($requestHeaderDTO->authorization);
        return $this->generateNotificationResponse($isTokenValid, $paymentNotificationRequestBodyDTO);
    }


    /**
     * Validates the TokenB2B received in the request.
     *
     * @param string $requestTokenB2B The TokenB2B received in the request.
     *
     * @return bool Returns `true` if the TokenB2B is valid, `false` otherwise.
     */
    public function validateTokenB2B(string $requestTokenB2B): bool
    {
        return $this->tokenB2BController->validateTokenB2B($requestTokenB2B, $this->publicKey);
    }

    /**
     * Validates the signature and generates a TokenB2B object accordingly.
     *
     * @param string $requestSignature The signature received in the request.
     * @param string $requestTimestamp The timestamp received in the request.
     *
     * @return void
     */
    public function validateSignatureAndGenerateToken(string $requestSignature, string $requestTimestamp): void
    {
        // Validate the signature
        $isSignatureValid = $this->validateSignature($requestSignature, $requestTimestamp, $this->privateKey, $this->clientId);

        // Generate a TokenB2B object based on the signature validity and set token
        $notificationTokenDTO = $this->generateTokenB2B($isSignatureValid);
        $notificationTokenBodyDTO = $notificationTokenDTO->body;
        $this->tokenB2B = $notificationTokenBodyDTO->accessToken;
    }

    /**
     * Generates a TokenB2B object based on the validity of the signature.
     *
     * @param bool $isSignatureValid Determines if the signature is valid.
     * @return NotificationTokenDTO The generated TokenB2B object.
     */
    public function generateTokenB2B(bool $isSignatureValid): NotificationTokenDTO
    {
            if($isSignatureValid){
                    return $this->tokenB2BController->generateTokenB2B($this->tokenB2BExpiresIn, $this->issuer, $this->privateKey, $this->clientId);
            }else{
                    return $this->tokenB2BController->generateInvalidSignatureResponse();
            }
    }
}