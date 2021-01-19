<?php


class ZohoOAuth
{
    public $redirectURL, $permissions;

    public function __construct()
    {
        $this->redirectURL = "https://{$_SERVER['HTTP_HOST']}/YmU6HrW91fuwciuN.php?dispatch=nm_title.zohoReceiveCode";
        $this->permissions = 'Desk.tickets.ALL,Desk.contacts.CREATE,Desk.contacts.WRITE,Desk.basic.READ,Desk.basic.CREATE';

    }

    public function getAuthURL($clientID)
    {
        $params = [
            'response_type' => 'code',
            'access_type' => 'offline',
            'client_id' => $clientID,
            'scope' => $this->permissions,
            'redirect_uri' => $this->redirectURL
        ];
        $encodedParams = http_build_query($params);
        return "https://accounts.zoho.eu/oauth/v2/auth?{$encodedParams}";
    }

    /**
     * @param $code
     * @param $clientID
     * @param $clientSecret
     * @return mixed
     * @throws Exception
     */
    public function getTokens($code, $clientID, $clientSecret)
    {
        $params = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'client_id' => $clientID,
            'client_secret' => $clientSecret,
            'redirect_uri' => $this->redirectURL
        ];
        $tokens = $this->sendRequest($params);

        if (!$tokens->access_token) {
            throw new Exception('missing access_token in response');
        }
        if (!$tokens->refresh_token) {
            throw new Exception('missing refresh_token in response');
        }
        if (!$tokens->expires_in) {
            throw new Exception('missing expires_in in response');
        }
        return $tokens;
    }

    public function refreshToken()
    {
        $params = [
            'refresh_token' => ZohoAuthSettings::getRefreshToken(),
            'client_id' => ZohoAuthSettings::getClientID(),
            'client_secret' => ZohoAuthSettings::getClientSecret(),
            'scope' => $this->permissions,
            'redirect_uri' => $this->redirectURL,
            'grant_type' => 'refresh_token'
        ];

        $tokens = $this->sendRequest($params);

        if (!$tokens->access_token) {
            throw new Exception('missing access_token in response');
        }
        if (!$tokens->expires_in) {
            throw new Exception('missing expires_in in response');
        }

        ZohoAuthSettings::writeAccessToken($tokens->access_token);
        ZohoAuthSettings::writeExpireTime($tokens->expires_in);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function sendRequest($params = [])
    {
        $curl = curl_init();

        $encodedParams = http_build_query($params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://accounts.zoho.eu/oauth/v2/token?{$encodedParams}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        try {
            $response = json_decode($response);
        } catch (Exception $exception) {
            throw new Exception('Error while request to ZOHO');
        }
        if (!$response) {
            throw new Exception('Error while request to ZOHO');
        }


        return $response;
    }
}