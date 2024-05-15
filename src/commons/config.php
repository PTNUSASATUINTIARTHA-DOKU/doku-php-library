<?php

const SANDBOX_BASE_URL = 'https://api-sandbox.doku.com';
const PRODUCTION_BASE_URL = 'https://api.doku.com';
 
const ACCESS_TOKEN = '/authorization/v1/access-token/b2b';

/**
 * Get the base URL based on production or sandbox environment
 * @param bool $isProduction determines if the environments production or not
 * @return string the Base URL based on environment
 */
function getBaseURL($isProduction) {
    $url = $isProduction ? PRODUCTION_BASE_URL : SANDBOX_BASE_URL;
    return $url . ACCESS_TOKEN;
}

