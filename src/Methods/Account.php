<?php

namespace AmoCrm\Methods;

use AmoCrm\CurlClient;

class Account
{
    protected $domain;
    protected $accessToken;

    public function __construct($domain, $token_data) {
        $this->domain = $domain . '/api/v2/account';

        $token_data = is_array($token_data) ? $token_data['access_token'] : $token_data;
        $this->accessToken = strpos($token_data, 'Bearer ') !== false ? $token_data : 'Bearer ' . $token_data;
    }

    public function get($params = '') {
        $client = new CurlClient();
        $headers = ['Content-Type'  => 'application/json', 'Authorization' => $this->accessToken];
        return $client->request('GET', $this->domain, $params, [], $headers);
    }
}