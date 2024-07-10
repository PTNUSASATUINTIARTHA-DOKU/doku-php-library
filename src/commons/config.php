<?php

const SANDBOX_BASE_URL = 'https://api-uat.doku.com';
const PRODUCTION_BASE_URL = 'https://api.doku.com';
 
const ACCESS_TOKEN = '/authorization/v1/access-token/b2b';
const CREATE_VA = '/virtual-accounts/bi-snap-va/v1/transfer-va/create-va';
const UPDATE_VA_URL = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/update-va';
const DELETE_VA_URL = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/delete-va';
const CHECK_VA = '/orders/v1.0/transfer-va/status';

/**
 * Get the base URL based on production or sandbox environment
 * @param bool $isProduction determines if the environments production or not
 * @return string the Base URL based on environment
 */
function getBaseURL($isProduction) {
    $url = $isProduction ? PRODUCTION_BASE_URL : SANDBOX_BASE_URL;
    return $url;
}

