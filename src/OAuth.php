<?php /** @noinspection PhpUnused */

namespace AmoCrm;

use AmoCrm\Exception\EmptyParamException;

class OAuth
{
    const TYPE_CODE = 'code';
    const TYPE_REFRESH = 'refresh';

    protected $domain;
    protected $grantType;
    protected $clientId;
    protected $clientSecret;
    protected $tokenCode;
    protected $redirectUri;

    public function __construct($domain, $type) {
        $this->domain = $domain . '/oauth2/access_token';
        $this->grantType = $type;
    }

    public function authorize($clientId, $clientSecret, $tokenCode, $redirectUri) {
        if (empty($clientId)) {
            throw new EmptyParamException('Client ID');
        }
        $this->clientId = $clientId;

        if (empty($clientSecret)) {
            throw new EmptyParamException('Client Secret');
        }
        $this->clientSecret = $clientSecret;

        if (empty($tokenCode)) {
            $which = $this->grantType === self::TYPE_CODE ? 'Authorization code' : 'Refresh token';
            throw new EmptyParamException($which);
        }
        $this->tokenCode = $tokenCode;

        if (empty($redirectUri)) {
            throw new EmptyParamException('Redirect URI');
        }
        $this->redirectUri = $redirectUri;

        $headers = ['Content-Type' => 'application/json'];

        $client = new CurlClient();
        return $client->request(
            'POST',
            $this->domain,
            '',
            $this->grantType === self::TYPE_CODE ? $this->withCode() : $this->withRefresh(),
            $headers
        );
    }

    protected function withCode() {
        return [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'authorization_code',
            'code'          => $this->tokenCode,
            'redirect_uri'  => $this->redirectUri,
        ];
    }

    protected function withRefresh() {
        return [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->tokenCode,
            'redirect_uri'  => $this->redirectUri,
        ];
    }
}