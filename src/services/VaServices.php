<?php

class VaServices
{
    /**
     * Create a virtual account by making a request to the DOKU API
     *
     * @param CreateVaRequestDTO $requestDto The request DTO
     * @param string $accessToken The access token
     * @param bool $isProduction Whether to use the production or sandbox environment
     * @return CreateVaResponseDTO
     * @throws Exception If there is an error creating the virtual account
     */
    public function createVa(CreateVaRequestDTO $requestDto, string $accessToken, bool $isProduction): CreateVaResponseDTO
    {
        // Determine the API endpoint based on the environment
        $apiEndpoint = $isProduction ? 'https://api.doku.com/v2/virtual-account' : 'https://sandbox.doku.com/v2/virtual-account';

        // Prepare the request headers
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // Prepare the request payload
        $payload = [
            'partner_service_id' => $requestDto->partnerServiceId,
            'customer_no' => $requestDto->customerNo,
            'virtual_account_no' => $requestDto->virtualAccountNo,
            'virtual_account_name' => $requestDto->virtualAccountName,
            'virtual_account_email' => $requestDto->virtualAccountEmail,
            'virtual_account_phone' => $requestDto->virtualAccountPhone,
            'trx_id' => $requestDto->trxId,
            'total_amount' => [
                'value' => $requestDto->totalAmount->value,
                'currency' => $requestDto->totalAmount->currency,
            ],
            'virtual_account_trx_type' => $requestDto->virtualAccountTrxType,
            'expired_date' => $requestDto->expiredDate,
            'additional_info' => [
                'channel' => $requestDto->additionalInfo->channel,
                'virtual_account_config' => [
                    'reusable_status' => $requestDto->additionalInfo->virtualAccountConfig->reusableStatus,
                ],
            ],
        ];
        $payloadJson = json_encode($payload);

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Send the request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);