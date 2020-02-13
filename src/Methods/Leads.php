<?php

namespace AmoCrm\Methods;

use AmoCrm\CurlClient;
use AmoCrm\Exception\EmptyParamException;

class Leads
{
    protected $domain;
    protected $accessToken;

    public $leads = [];

    public function __construct($domain, $token_data) {
        $this->domain = $domain . '/api/v2/leads';

        $token_data = is_array($token_data) ? $token_data['access_token'] : $token_data;
        $this->accessToken = strpos($token_data, 'Bearer ') !== false ? $token_data : 'Bearer ' . $token_data;
    }

    public function add() {
        $client = new CurlClient();
        $headers = ['Content-Type'  => 'application/json', 'Authorization' => $this->accessToken];
        return $client->request('POST', $this->domain, [], ['add' => $this->leads], $headers);
    }

    public function addLead($data) {
        if (empty($data['name'])) {
            throw new EmptyParamException('Name');
        }
        $this->leads[] = $data;
    }

    public function update() {
        $client = new CurlClient();
        $headers = ['Content-Type'  => 'application/json', 'Authorization' => $this->accessToken];
        return $client->request('POST', $this->domain, [], ['update' => $this->leads], $headers);
    }

    public function updateLead($data) {
        if (empty($data['id'])) {
            throw new EmptyParamException('ID');
        }
        if (empty($data['updated_at'])) {
            $data['updated_at'] = time();
        }
        $this->leads[] = $data;
    }

    public function get($params = '') {
        $client = new CurlClient();
        $headers = ['Content-Type'  => 'application/json', 'Authorization' => $this->accessToken];
        return $client->request('GET', $this->domain, $params, [], $headers);
    }
}