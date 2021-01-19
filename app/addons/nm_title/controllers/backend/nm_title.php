<?php

use Tygh\Settings;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/** @var string $mode */
$ZohoOAuth = new ZohoOAuth();
$clientID = ZohoAuthSettings::getClientID();
$clientSecret = ZohoAuthSettings::getClientSecret();
echo '<pre>';

if ($mode == 'zohoRequestCode') {
    if (!$clientID){
        echo "<b>Internal error: clientID is not found</b>";
        die();
    }
    $linkToOAuth = $ZohoOAuth->getAuthURL($clientID);
    echo "<a href='{$linkToOAuth}'>Click to connect ZOHO</a><br>";
}

if ($mode == 'zohoReceiveCode') {
    $code = $_REQUEST['code'];
    $tokens = $ZohoOAuth->getTokens($code, $clientID, $clientSecret);

    ZohoAuthSettings::writeAccessToken($tokens->access_token);
    ZohoAuthSettings::writeRefreshToken($tokens->refresh_token);
    ZohoAuthSettings::writeExpireTime($tokens->expires_in);

    echo 'ALL IS OK, tokens received';
}

exit();