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
    const ACCESS_TOKEN_B2B2C = '/authorization/v1/access-token/b2b2c';
    const DIRECT_DEBIT_PAYMENT_URL = '/direct-debit/core/v1/debit/payment-host-to-host';
    const DIRECT_DEBIT_ACCOUNT_BINDING_URL = '/direct-debit/core/v1/registration-account-binding';
    const DIRECT_DEBIT_ACCOUNT_UNBINDING_URL = '/direct-debit/core/v1/registration-account-unbinding';
    const DIRECT_DEBIT_CARD_UNBINDING_URL = '/direct-debit/core/v1/registration-card-unbind';
    const CARD_REGISTRATION_URL = '/direct-debit/v2/card-registration';
    const DIRECT_DEBIT_REFUND_URL = '/direct-debit/v1/refund';
    const DIRECT_DEBIT_BALANCE_INQUIRY_URL= '/direct-debit/core/v1/balance-inquiry';
    const DIRECT_DEBIT_CHECK_STATUS_URL = '/orders/v1.0/debit/status';
    const DIRECT_DEBIT_PAYMENT_NOTIF_URL = '/v1.0/debit/notify';

    public static function getBaseURL($isProduction) {
            $url = $isProduction ? Config::PRODUCTION_BASE_URL : Config::SANDBOX_BASE_URL;
            return $url;
    }
}



