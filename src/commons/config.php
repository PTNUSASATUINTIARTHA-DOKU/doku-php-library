<?php

const SANDBOX_BASE_URL = 'https://api-sandbox.doku.com';
const PRODUCTION_BASE_URL = 'https://api.doku.com';
 
const ACCESS_TOKEN = '/api/v1/access-token/b2b2c'; //tanya

/**
 * Get the base URL based on production or sandbox environment
 * @param bool $isProduction determines if the environments production or not
 * @return string the Base URL based on environment
 */
function getBaseURL($isProduction) {
    return $isProduction ? PRODUCTION_BASE_URL : SANDBOX_BASE_URL;
}

