<?php

namespace Doku\Snap\Commons;

class Config {
    const SANDBOX_BASE_URL = 'https://api-uat.doku.com';
    const PRODUCTION_BASE_URL = 'https://api.doku.com';
    
    const ACCESS_TOKEN = '/authorization/v1/access-token/b2b';
    const CREATE_VA = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va';
    const UPDATE_VA_URL = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/update-va';
    const DELETE_VA_URL = '/virtual-accounts/bi-snap-va/v1.1/transfer-va/delete-va';
    const CHECK_VA = '/orders/v1.0/transfer-va/status';

    public static function getBaseURL($isProduction) {
            $url = $isProduction ? Config::PRODUCTION_BASE_URL : Config::SANDBOX_BASE_URL;
            return $url;
    }
}



