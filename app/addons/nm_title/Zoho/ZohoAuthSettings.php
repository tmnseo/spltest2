<?php

use Tygh\Settings;

class ZohoAuthSettings
{
    public static function getClientID()
    {
        return Settings::instance()->getValue('zoho_client_id', 'nm_title');
    }

    public static function getClientSecret()
    {
        return Settings::instance()->getValue('zoho_client_secret', 'nm_title');
    }

    public static function getRefreshToken()
    {
        return Settings::instance()->getValue('zoho_refresh_token', 'nm_title');
    }

    public static function getExpireTime()
    {
        return Settings::instance()->getValue('zoho_token_expires_in', 'nm_title');
    }

    public static function getAccessToken()
    {
        return Settings::instance()->getValue('zoho_access_token', 'nm_title');
    }

    public static function writeAccessToken($newAccessToken)
    {
        return Settings::instance()->updateValue('zoho_access_token', $newAccessToken, 'nm_title');
    }

    public static function writeRefreshToken($newRefreshToken)
    {
        return Settings::instance()->updateValue('zoho_refresh_token', $newRefreshToken, 'nm_title');
    }

    public static function writeExpireTime($timeToDelay)
    {
        $endTime = time() + $timeToDelay;
        return Settings::instance()->updateValue('zoho_token_expires_in', $endTime, 'nm_title');
    }
}